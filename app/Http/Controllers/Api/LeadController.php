<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Lead\Lead;
use App\Models\DailyTrack;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Lead\LeadRepository;

use App\Models\History\History;
use App\Repositories\Backend\History\EloquentHistoryRepository;

class LeadController extends Controller
{
    public $leadRepository = '';

    public function __construct(LeadRepository $leadRepository)
    {
       $this->leadRepository = $leadRepository;
    }

    public function trackStatus(Request $request)
    {
        $user = $request->user();
        $date = date('Y-m-d');
        $track = DailyTrack::where('user_id', $user->id)
                            ->where('date', $date)
                            ->first();
        $checkIn = $checkOut = false;
        if(!isset($track->in) || $track->in == null)
        {
            $checkIn = true;
        }elseif($track->out == null && $track->in != null)
        {
            $checkOut = true;
        }

        $data = array('checkIn' => $checkIn, 'checkOut' => $checkOut);
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    public function checkIn(Request $request)
    {
        $this->leadRepository->userDailyTrack($request->user(), 'in');
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => array()])->setStatusCode(200);
    }

    public function checkOut(Request $request)
    {
        $this->leadRepository->userDailyTrack($request->user(), 'out');
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => array()])->setStatusCode(200);
    }

    public function callList(Request $request)
    {
        $data = $this->leadRepository->getCallListForAPI($request, '0');
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    public function followUpList(Request $request)
    {
        $data = $this->leadRepository->getFollowUpListForAPI($request, '0');
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    public function callHistory(Request $request)
    {
        $data = $this->leadRepository->getCallHistoryForAPI($request);
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    public function filters(Request $request)
    {
        $sources = $this->leadRepository->getMediumFilter();
        $data = array(
            'sources' => $sources,
            'phase' => array(
                "buy_attempt"       => 'Buy Attempt',
                "cart"              => 'Cart Abandon',
                "trial"             => 'Trial Started',
                "kit_purchased"     => 'Kit Purchased'
            ),
            'stage' => array(
                "new"               => 'New Leads',
                "followUp"          => 'Follow Up Only',
                "open"              => 'Open',
                "sale"              => 'Sale',
                "hot"               => 'Hot',
                "mild"              => 'Mild',
                "cold"              => 'Cold',
                "no_answer"         => 'No Answer',
                "busy"              => 'Busy',
                "not_interested"    => 'Not Interested',
                "dead"              => 'Dead',
            )
        );
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    function leadDetail(Lead $lead, Request $request)
    {
        //Get active subscriptions
        $subscriptions = collect([]);

        //check learning status on login server and update lead
        $loginServerUser =  DB::connection('login')
                                ->table(config('table.login.users'))
                                ->where('country_code', $lead->country_code)
                                ->where('phone', $lead->phone)
                                ->first();
        if($loginServerUser != null)
        {
            $lead->login_id = $loginServerUser->id;
            $lead->messenger = $loginServerUser->messenger;
            $lead->learning = $loginServerUser->learning;
            $lead->learning_id = $loginServerUser->learning_id;
            $lead->update();
        }

        if($lead->learning != '0' && $lead->learning_id != '0')
        {
            $subscriptions = DB::connection('learning')
                        ->table(config('table.learning.subscriptions').' as s')
                        ->leftJoin(config('table.learning.children').' as c', 'c.id', '=', 's.child_id')
                        ->leftJoin(config('table.learning.packages').' as p', 'p.id', '=', 's.packages_id')
                        ->leftJoin(config('table.learning.package_orders').' as po', 'po.order_id', '=', 's.order_id')
                        ->where('s.user_id', $lead->learning_id)
                        ->where('s.subscription_status', 'ACTIVE')
                        ->select('s.alias', 'p.package_name', 's.subscription_type', 'po.package_addons_id', 'c.child_name', 'c.child_class', 's.created_at')
                        ->get();
            $leadStage = '2';
            if($subscriptions->count() > 0)
            {
                $leadStage = '3';
                $lead->subscription_type = 'FREE';
                if($subscriptions->where('subscription_type', 'PAID')->count() > 0)
                {
                    $leadStage = '4';
                    $lead->subscription_type = 'PAID';
                }
            }
            $lead->lead_stage = $leadStage;
            $lead->update();
        }
        
        $address = '';
        if($lead->login_id != null && $lead->login_id != 0)
        {
            $loginUser = DB::connection('login')
                                ->table(config('table.login.users').' as u')
                                ->where('u.id', $lead->login_id)
                                ->first();
            $address = prepareUserLocation($loginUser);
        }

        $children = array();
        if($lead->learning != '0' && $lead->learning_id != '0' && $lead->login_id != '0')
        {
            $requestID = $this->generateLoginRequestId();

            //hit api
            $postData = array( 'UserID' => $lead->login_id );
            $response = Curl::to(env('LOGIN_APP_URL').'/api/crm/getChildren')
                        ->withHeader('RequestID: '.$requestID)
                        ->withData(['Data' => encryptData($postData)])
                        ->asJson()
                        ->post();
            if(isset($response->Status) && $response->Status == '200')
            {
                $children = json_decode(decryptData($response->Data));
            }

            // remove request
            $this->removeLoginRequestId($requestID);
        }

        $data = array(
            'subscriptions' => $subscriptions,
            'children' => $children,
            'address' => $address,
            'lead' => $lead,
            'alternateNumbers' => $lead->alternateNumbers()->get(),
        );
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }

    function leadHistory(Lead $lead, Request $request, EloquentHistoryRepository $eloquentHistoryRepository)
    {
        $result = array();
        $history = History::where('entity_id', $lead->id)->where('type_id', '4');
        $history = $eloquentHistoryRepository->buildPagination($history, null, true, 30);
        foreach($history as $historyItem)
        {
            
            if($historyItem->type_id == '4' && $historyItem->sub_type == 'call'){
                if($historyItem->call->exotel_sid != null && $historyItem->call->exotel_sid != ''){
                    $callDuration = ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : '';
                }else{
                    $callDuration = ($historyItem->call->duration != null)? duration($historyItem->call->duration) : '';
                }

                $callRecording = null;
                if($historyItem->call->exotel_sid != null && $historyItem->call->exotel_sid != '') {
                    if($historyItem->call->call_record_file != null) {
                        $callRecording = $historyItem->call->call_record_file;
                    }
                }else{
                    if($historyItem->call->call_record_file != null) {
                        $callRecording = url('storage/call_records/'.$historyItem->call->call_record_file);
                    }
                }

                $result[] = array(
                    'call_id'           => $historyItem->call->id,
                    'sub_type'          => $historyItem->sub_type,
                    'icon'              => $historyItem->icon,
                    'call_by'           => $historyItem->call->user->name,
                    'call_duration'     => $callDuration,
                    'call_agenda'       => ucfirst($historyItem->call->call_type),
                    'time'              => $historyItem->call->created_at->format(config('access.date_time_format')),
                    'exotel_sid'        => $historyItem->call->exotel_sid,
                    'cloud_call_status' => $historyItem->call->exotel_call_status,
                    'call_recording'    => $callRecording,
                    'call_saved'        => $historyItem->call->saved,
                    'lead_status'       => leadStatus($historyItem->call->lead_status),
                    'note'              => $historyItem->call->note,
                );
            }else if($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call'){
                $callRecording = null;
                if($historyItem->call_record->acrfilename != null) {
                    $callRecording = url('storage/call_records/'.$historyItem->call_record->acrfilename);
                }
                $result[] = array(
                    'sub_type'          => $historyItem->sub_type,
                    'icon'              => $historyItem->icon,
                    'call_by'           => $historyItem->call_record->user->name,
                    'call_duration'     => ($historyItem->call_record->duration != null)? duration($historyItem->call_record->duration) : '',
                    'call_agenda'       => '',
                    'time'              => $historyItem->call_record->created_at->format(config('access.date_time_format')),
                    'call_recording'    => $callRecording,
                );
            }else if($historyItem->type_id == '4' && $historyItem->sub_type == 'note'){
                $result[] = array(
                    'sub_type'          => $historyItem->sub_type,
                    'icon'              => $historyItem->icon,
                    'added_by'          => $historyItem->note->user->name,
                    'time'              => $historyItem->note->created_at->format(config('access.date_time_format')),
                    'note'              => $historyItem->note->note,
                );
            }else if($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record'){
                $callRecording = null;
                if($historyItem->call_record->acrfilename != null) {
                    $callRecording = url('storage/call_records/'.$historyItem->call_record->acrfilename);
                }
                $result[] = array(
                    'sub_type'          => $historyItem->sub_type,
                    'icon'              => $historyItem->icon,
                    'call_by'           => $historyItem->user->name,
                    'call_duration'     => ($historyItem->call_record->duration != null)? duration($historyItem->call_record->duration) : '',
                    'time'              => $historyItem->call_record->created_at->format(config('access.date_time_format')),
                    'call_recording'    => $callRecording,
                );
            }else{
                $result[] = array(
                    'sub_type'  => $historyItem->sub_type,
                    'icon'      => $historyItem->icon,
                    'text'      => $eloquentHistoryRepository->renderDescription($historyItem->text, $historyItem->assets),
                );
            }
        }

        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => array('results' => $result)])->setStatusCode(200);
    }

    function addLeadNote(Lead $lead, Request $request)
    {
        $user = $request->user();
        $this->leadRepository->addLeadNote($lead, $user, $request);

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => []]);
    }

    function searchLead(Request $request)
    {
        $data = $this->leadRepository->searchLeadApi($request);
        return response()->json(['Status' => 200, 'Message' => 'Success', 'Data' => $data])->setStatusCode(200);
    }
}
