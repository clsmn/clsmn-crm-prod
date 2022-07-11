<?php

namespace App\Http\Controllers\Backend\Lead;

use Storage;
use App\Models\Lead\Lead;
use App\Models\DailyTrack;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Models\Data\DataUser;
use App\Models\Data\DataChild;
use App\Models\Lead\CallHistory;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Events\Backend\Lead\LeadOpened;
use App\Events\Backend\Lead\LeadCalled;
use App\Models\AlternateNumber\AlternateNumber;
use App\Repositories\Backend\Lead\LeadRepository;
use App\Repositories\Backend\DataUser\DataUserRepository;
use Auth;
/**
 * Class LeadController.
 */
class LeadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, DataUserRepository $dataBankRepo, LeadRepository $leadRepo)
    {
        $leadStage = DB::table('lead_stage')->pluck('message', 'id');
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
        // $cities = $leadRepo->getCitiesFilter();
        $sources = $leadRepo->getMediumFilter();

        return view('backend.lead.index', compact('track', 'leadStage', 'checkIn', 'checkOut', 'sources'));
    }
    public function cloudCall(Request $request)
    {
        // $user = $request->user();
        // echo  date('Y-m-d',strtotime("-2 day"));
        // die("****");
        $office24response = DB::table('office24response')->select('response')->where('user','!=','')->where('call_time','>=', date('Y-m-d',strtotime("-2 day")))->orderBy('call_time','desc')->get();

        // $office24response =  DB::table('office24response')
        //             ->select('office24response.*','users.*')
        //             ->join('users', 'users.office24by_username', '=', 'office24response.user')
        //             ->where('office24response.user','!=','')
        //             ->where('office24response.call_time','>=', date('Y-m-d',strtotime("-2 day")))
        //             ->get();

       
        // print_r($office24response);
        // die("******");
        return view('backend.lead.cloudCall', compact('office24response'));
    }
    function callHistory(Request $request, LeadRepository $leadRepo)
    {
        $leadStage = DB::table('lead_stage')->pluck('message', 'id');
        $user = $request->user();
        if($user->hasRole('Manager'))
        {
            $executives = User::whereHas('roles', function($query){
                                $query->where('role_id', '3');
                                $query->orWhere('role_id', '2');
                            })
                            ->where('status', '1')
                            ->pluck('name', 'id');
            $leadStatus = $request->get('status');
            $sources = $leadRepo->getMediumFilter();
            // $cities = $leadRepo->getCitiesFilter();

            return view('backend.lead.manager', compact('leadStage', 'executives', 'leadStatus', 'sources')); 
        }
    }

    function getLead(Lead $lead, Request $request)
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
        
        $packages = DB::connection('learning')
                        ->table(config('table.learning.packages'))
                        ->where('status', 'PUBLISHED')
                        ->select('id', 'package_name')
                        ->get();

        $address = '';
        if($lead->login_id != null && $lead->login_id != 0)
        {
            $loginUser = DB::connection('login')
                                ->table(config('table.login.users').' as u')
                                ->where('u.id', $lead->login_id)
                                ->first();
            $address = prepareUserLocation($loginUser);
        }
        //event(new LeadOpened($lead, $request->user()));

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

        $executives = User::whereIn('id', [24,272])
                    ->pluck('name', 'id');
                    
        $user = $request->user();
        $total_history_count = DB::table('call_history')->select('id')->where('lead_id',$lead->id)->where('data_medium','FBL_REM_MS')->count();

        $total_history_no_answer_count = DB::table('call_history')->select('id')->where('lead_id',$lead->id)->where('lead_status','no_answer')->where('data_medium','FBL_REM_MS')->count();
        return view('backend.lead.show', compact('lead', 'address', 'packages', 'subscriptions', 'children', 'executives', 'user','total_history_count','total_history_no_answer_count'));
    }

    function setPrimaryNumber(Request $request, LeadRepository $leadRepository)
    {
        $leadId = $request->get('leadId');
        $type = $request->get('type');
        $id = $request->get('id');

        if($type == 'alternate')
        {
            $lead = Lead::findOrFail($leadId);
            $alternateNumber = $lead->alternateNumbers()->where('id', $id)->first();
            $canSetPrimary = $leadRepository->leadChangeNumber($alternateNumber->phone, $lead->phone, $alternateNumber->name, $alternateNumber->relation);
            if(!$canSetPrimary)
            {
                return response()->json(['Message' => 'Lead already assigned.', 'Status' => '201']);
            }

            return response()->json(['Message' => 'Success', 'Status' => '200']);
        }
    }

    function addAlternateNumber(Lead $lead, Request $request, LeadRepository $leadRepository)
    {
        $newNumber = str_replace('-', '', $request['phone']);

        if($request['preferred'] == '1')
        {
            $canSetPrimary = $leadRepository->leadChangeNumber($newNumber, $lead->phone, $request['name'], $request['relation']);
            if(!$canSetPrimary)
            {
                $alternateNumber = new AlternateNumber;
                $alternateNumber->lead_id = $lead->id;
                $alternateNumber->phone = $newNumber;
                $alternateNumber->name = $request['name'];
                $alternateNumber->preferred = '0';
                $alternateNumber->relation = $request['relation'];
                $alternateNumber->save();

                return response()->json(['Message' => 'Lead already assigned.', 'Status' => '201', 'Data' => $alternateNumber]);
            }
        }else{
            $alternateNumber = new AlternateNumber;
            $alternateNumber->lead_id = $lead->id;
            $alternateNumber->phone = $newNumber;
            $alternateNumber->name = $request['name'];
            $alternateNumber->preferred = '0';
            $alternateNumber->relation = $request['relation'];
            $alternateNumber->save();
            return response()->json(['Message' => 'Success', 'Status' => '202', 'Data' => $alternateNumber]);
        }

        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    function updateLeadAddress(Lead $lead, Request $request)
    {
        if($lead->login_id != null && $lead->login_id != 0)
        {
            DB::connection('login')
                ->table(config('table.login.users').' as u')
                ->where('u.id', $lead->login_id)
                ->update([
                    'lat_long' => $request['lat_long'],
                    'locality' => $request['locality'],
                    'city' => $request['city'],
                    'state' => $request['state'],
                    'country' => $request['country'],
                ]);
        }

        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    function removeChild(DataChild $child)
    {
        $child->delete();

        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    function updateDataChild(DataChild $child, Request $request)
    {
        //Update Child
        $child->name = $request['childName'];
        $child->grade = $request['childClass'];
        $child->dob = NULL;
        if(isset($request['childDob']) && $request['childDob'] != '')
        {
            $arr = explode('/', $request['childDob']);
            $child->dob = $arr[2].'-'.$arr[1].'-'.$arr[0];
        }
        $child->gender = (isset($request['childGender']) && $request['childGender']!='')? $request['childGender'] : NULL;
        $child->school_name = $request['childSchool'];
        $child->update();

        //Return child values
        $data = array(
            'childName' => $child->name,
            'childGender' => $child->gender,
            'childAge' => ($child->age != null && $child->age != 0)? $child->age.' '.trans_choice('strings.backend.general.years', $child->age): '',
            'childClass' => getClassGradeName($child->grade),
            'childSchool' => $child->school_name
        );

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }

    function addDataChild(Lead $lead, Request $request)
    {
        //Add Child
        $child = new DataChild;
        $child->data_user_id    = $lead->data_user_id;
        $child->data_medium     = 'call';
        $child->status          = '0';
        $child->added_on        = date('Y-m-d H:i:s');
        $child->name            = $request['childName'];
        $child->grade           = $request['childClass'];
        $child->dob             = NULL;
        if(isset($request['childDob']) && $request['childDob'] != '')
        {
            $arr = explode('/', $request['childDob']);
            $child->dob = $arr[2].'-'.$arr[1].'-'.$arr[0];
        }
        $child->gender          = (isset($request['childGender']) && $request['childGender']!='')? $request['childGender'] : NULL;
        $child->school_name     = $request['childSchool'];
        $child->save();

        //Return child values
        $data = array(
            'childId'       => $child->id,
            'childName'     => $child->name,
            'childGender'   => $child->gender,
            'childDob'      => ($child->dob !=null)? $child->dob->format('d/m/Y') : '',
            'childAge'      => ($child->age != null && $child->age != 0)? $child->age.' '.trans_choice('strings.backend.general.years', $child->age): '',
            'childGrade'    => $child->grade,
            'childClass'    => getClassGradeName($child->grade),
            'childMedium'   => ucfirst($child->data_medium),
            'childSchool'   => $child->school_name,
            'childAdded'    => $child->added_on->format(config('access.date_format')),
        );

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }

    function activateLearning(Lead $lead, Request $request)
    {
        $children = array();
        if($lead->learning == '0' && $lead->learning_id == '0')
        {
            $requestID = $this->generateLoginRequestId();

            //hit api
            $postData = array( 'CountryCode' => $lead->country_code, 'Phone' => $lead->phone );
            $response = Curl::to(env('LOGIN_APP_URL').'/api/crm/activateLearning')
                        ->withHeader('RequestID: '.$requestID)
                        ->withData(['Data' => encryptData($postData)])
                        ->asJson()
                        ->post();
            if(isset($response->Status) && $response->Status == '200')
            {
                $response->Data = json_decode(decryptData($response->Data));
                //update learning id for lead
                $lead->login_id = $response->Data->user->id;
                $lead->learning = $response->Data->user->learning;
                $lead->learning_id = $response->Data->user->learning_id;
                $lead->update();

                $children = $response->Data->children;
            }

            // remove request
            $this->removeLoginRequestId($requestID);
        }

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => array('lead' => $lead, 'children' => $children)]);
    }

    function getLearningChildren(Lead $lead, Request $request)
    {
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

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $children]);
    }

    function startLearningTrial(Lead $lead, Request $request)
    {
        $subscription = array();
        if($lead->learning != '0' && $lead->learning_id != '0' && $lead->login_id != '0')
        {
            $requestID = $this->generateLoginRequestId();

            $childID = $request['childId'];
            $childDob = NULL;
            if(isset($request['childDob']) && $request['childDob'] != '')
            {
                $arr = explode('/', $request['childDob']);
                $childDob = $arr[2].'-'.$arr[1].'-'.$arr[0];
            }
            $postData = array( 
                'UserID' => $lead->login_id,  
                'ChildId' => $childID,  
                'ChildName' => $request['childName'],  
                'ChildClass' => $request['childClass'],  
                'ChildDob' => $childDob,  
            );
            if($childID == '' || $childID == '0')
            {
                //add child
                $response = Curl::to(env('LOGIN_APP_URL').'/api/crm/addChild')
                            ->withHeader('RequestID: '.$requestID)
                            ->withData(['Data' => encryptData($postData)])
                            ->asJson()
                            ->post();
                if(isset($response->Status) && $response->Status == '200')
                {
                    $response->Data = json_decode(decryptData($response->Data));
                    $childID = $response->Data->childId;
                }else{
                    return response()->json(['Message' => $response->Message, 'Status' => $response->Status]);
                }
            }else{
                //update child
                $response = Curl::to(env('LOGIN_APP_URL').'/api/crm/childUpdate')
                            ->withHeader('RequestID: '.$requestID)
                            ->withData(['Data' => encryptData($postData)])
                            ->asJson()
                            ->post();
            }

            $this->removeLoginRequestId($requestID);

            //Create cart and start free trial
            $requestID = $this->generateLearningRequestId();

            $cartId = 0;
            //Cart Create
            $postData = array( 
                'UserID' => $lead->learning_id,  
                'ChildId' => $childID,  
                'PackagesId' => $request['packageId'],  
                'PackageType' => 'DIGITAL',  
                'PackageAddonId' => '0',  
                'ReferralCode' => '',  
                'ReferralMedium' => '',  
            );
            $response = Curl::to(env('LEARNING_APP_URL').'/api/crm/cart')
                            ->withHeader('RequestID: '.$requestID)
                            ->withData(['Data' => encryptData($postData)])
                            ->asJson()
                            ->post();
            if(isset($response->Status) && $response->Status == '200')
            {
                $response->Data = json_decode(decryptData($response->Data));
                $cartId = $response->Data->id;
            }else{
                return response()->json(['Message' => $response->Message, 'Status' => $response->Status]);
            }

            //Subscription create
            $subscriptionAlias = '';
            $postData = array( 
                'UserID' => $lead->learning_id,  
                'ChildId' => $childID,  
                'CartId' => $cartId,    
            );
            $response = Curl::to(env('LEARNING_APP_URL').'/api/crm/subscriptions')
                            ->withHeader('RequestID: '.$requestID)
                            ->withData(['Data' => encryptData($postData)])
                            ->asJson()
                            ->post();
            if(isset($response->Status) && $response->Status == '200')
            {
                $response->Data = json_decode(decryptData($response->Data));
                $subscriptionAlias = $response->Data->subscription_id;
            }else{
                return response()->json(['Message' => $response->Message, 'Status' => $response->Status]);
            }

            $this->removeLearningRequestId($requestID);

            //return subscription detail for list.
            $subscription = DB::connection('learning')
                        ->table(config('table.learning.subscriptions').' as s')
                        ->leftJoin(config('table.learning.children').' as c', 'c.id', '=', 's.child_id')
                        ->leftJoin(config('table.learning.packages').' as p', 'p.id', '=', 's.packages_id')
                        ->leftJoin(config('table.learning.package_orders').' as po', 'po.order_id', '=', 's.order_id')
                        ->where('s.alias', $subscriptionAlias)
                        ->where('s.subscription_status', 'ACTIVE')
                        ->select('s.alias', 'p.package_name', 's.subscription_type', 'po.package_addons_id', 'c.child_name', 'c.child_class', 's.created_at')
                        ->first();
            $subscription->package_addons_id = 'No';
            $subscription->child_class = getClassGradeName($subscription->child_class);
            $subscription->created_at = date(config('access.date_format'), strtotime($subscription->created_at));
        }

        return response()->json(['Message' => 'Free trial has been started successfully.', 'Status' => '200', 'Data' => $subscription]);
    }

    function callLead(Lead $lead, Request $request, LeadRepository $leadRepository)
    {
        $user = $request->user();
        if($lead->assigned_to == $user->id)
        {
            //get Primary number
            $phone = $leadRepository->getPrimaryNumber($lead);

            $phone = str_replace('+91', '', $phone);
            $callHistory = new CallHistory;
            $callHistory->lead_id = $lead->id;
            $callHistory->country_code = $lead->country_code;
            $callHistory->phone = $phone;
            $callHistory->called_by = $user->id;
            $callHistory->lead_status = $lead->lead_status;
            $callHistory->data_medium = $lead->data_medium;
            
            //count repeat call
            $callCount = CallHistory::where('phone', $phone)->where('country_code', $lead->country_code)->count();
            if($callCount > 0)
            {
                $callHistory->type = 'repeat';
            }
            $callHistory->save();

            //trigger lead calling event
            event(new LeadCalled($lead, $user, $callHistory));

            //change value
            $callHistory->called_by = $user->name;
            $callHistory->called_at = $callHistory->created_at->format(config('access.date_time_format'));
            $callHistory->phone = substr_replace($phone, '-', 5, 0);
            $callHistory->lead_stage = $lead->lead_stage;

            return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $callHistory]);
        }
    }

    function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }

    function cloudCallLead(Lead $lead, Request $request, LeadRepository $leadRepository)
    {
        $user = $request->user();
        if($lead->assigned_to == $user->id)
        {
            //get Primary number
            $phone = $leadRepository->getPrimaryNumber($lead);

            $phone = str_replace('+91', '', $phone);

            $callHistory = new CallHistory;
            $callHistory->lead_id = $lead->id;
            $callHistory->country_code = $lead->country_code;
            $callHistory->phone = $phone;
            $callHistory->called_by = $user->id;
            $callHistory->lead_status = $lead->lead_status;
            $callHistory->data_medium = $lead->data_medium;

            // if($user->phone != NULL && $user->phone != '') {

                // OFFICE24BY7 CALLING CODE START

                $apiKey= 'a138e183-15b5-45c8-a05b-c2ab3f173be5';
                $agentloginid=  $user->office24by_username;
                $servienumber= '+912235155149';
                $customernumber= $phone;
                $format= 'json';
                $refrence_number = $this->guidv4();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://app.office24by7.com/v1/communication/API/clickToCall");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=".$apiKey."&agentloginid=".$agentloginid."&servienumber=".$servienumber."&customernumber=".$customernumber."&format=".$format."&referencestate=".$refrence_number);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $get_api_response = curl_exec($ch);
                curl_close($ch);
                $callHistory->office_ref_id = $refrence_number;

                // OFFICE24BY7 CALLING CODE ENDS


                // EXOTEL CODE START

                // if($user->phone == '9074251626'){
                //     $phone = '7053515290';
                // }
                // create call using exotel
                // $response = Curl::to('https://'.env('EXOTEL_API_KEY').':'.env('EXOTEL_API_TOKEN').'@'.env('EXOTEL_SUBDOMAIN').'/v1/Accounts/'.env('EXOTEL_SID').'/Calls/connect')
                //             ->withData([
                //                 'From' => '0'.$user->phone,
                //                 'To' => '0'.$phone,
                //                 // 'From' => '09074251626',
                //                 // 'To' => '07053515290',
                //                 'CallerId' => env('EXOTEL_CALLER_ID'),
                //                 'CallType' => 'trans',
                //                 'TimeLimit' => '1200'
                //             ])
                //             ->post();
                            
                // $responseXML = json_decode(json_encode(simplexml_load_string($response)));

                // if(!isset($responseXML->Call)){
                //     return response()->json(['Message' => $responseXML->RestException->Message, 'Status' => '422']);
                // }
                // $callHistory->exotel_sid = $responseXML->Call->Sid;

                // EXOTEL CODE END


            // }
            
            //count repeat call
            $callCount = CallHistory::where('phone', $phone)->where('country_code', $lead->country_code)->count();
            if($callCount > 0)
            {
                $callHistory->type = 'repeat';
            }
            $callHistory->save();

            //trigger lead calling event
            event(new LeadCalled($lead, $user, $callHistory));

            //change value
            $callHistory->called_by = $user->name;
            $callHistory->called_at = $callHistory->created_at->format(config('access.date_time_format'));
            $callHistory->phone = substr_replace($phone, '-', 5, 0);
            $callHistory->lead_stage = $lead->lead_stage;

            return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $callHistory]);
        }
    }

    function updateCallLead(CallHistory $callHistory, Request $request)
    {
        $source = $request->get('source');
        $user = $request->user();
        $lead = Lead::findOrFail($callHistory->lead_id);

        if($lead->assigned_to == $user->id)
        {
            if($source == 'api') {
                $nextFollowUp = ($request['nextFollowUpTime'] != '')? $request['nextFollowUpTime'] : NULL;
            }else{
                $nextFollowUp = ($request['nextFollowUpTime'] != '')? $request['nextFollowUpTime'].':00' : NULL;
            }

            //get call recording and details from exotel api
            if($callHistory->exotel_sid != '' && $callHistory->exotel_sid != null) {

                $response = Curl::to('https://'.env('EXOTEL_API_KEY').':'.env('EXOTEL_API_TOKEN').'@'.env('EXOTEL_SUBDOMAIN').'/v1/Accounts/'.env('EXOTEL_SID').'/Calls/'.$callHistory->exotel_sid)
                            ->get();
                $responseXML = json_decode(json_encode(simplexml_load_string($response)));

                $callHistory->exotel_call_status = $responseXML->Call->Status;
                //can't save call while its in process
                if('in-progress' == $responseXML->Call->Status){
                    return response()->json(['Message' => 'Please wait', 'Status' => '422']);
                }

                if('completed' == $responseXML->Call->Status) {
                    $callHistory->duration = $responseXML->Call->Duration;
                    $callHistory->call_record_file = $responseXML->Call->RecordingUrl;
                }
            }

            //Update call history
            $callHistory->call_type = $request['callAgenda'];
            $callHistory->note = $request['note'];
            $callHistory->lead_status = $request['leadStatus'];
            $callHistory->schedule_address = ($request['scheduleDemoAddress'] != '')? $request['scheduleDemoAddress'] : NULL;
            $callHistory->schedule_time = ($request['scheduleDemoTime'] != '')? $request['scheduleDemoTime'].':00' : NULL;
            $callHistory->next_follow_up = $nextFollowUp;
            $callHistory->saved = '1';
            $callHistory->update();

            $lastActivity = $callHistory->created_at->format('Y-m-d H:i:s');
            //Update lead
            
            $lead->lead_status = $request['leadStatus'];
            $lead->lead_status_at = $callHistory->updated_at;
            $lead->reason_id = ($request['leadDeadReason'] != '')? $request['leadDeadReason'] : '0';
            $lead->lead_stage = $request['leadStage'];
            $lead->last_call = date('Y-m-d H:i:s', strtotime($lastActivity));
            $lead->last_call_id = $callHistory->id;
            $lead->frequency = $lead->frequency + 1;
            $lead->next_follow_up = $nextFollowUp;
            $lead->last_activity = date('Y-m-d H:i:s', strtotime($lastActivity));
            $lead->done = '1';
            if($nextFollowUp != NULL && date('Y-m-d') == date('Y-m-d', strtotime($nextFollowUp)))
            {
                $lead->done = '0';
            }
            $lead->update();

            //update lead last call in data user table
            $dataUser = DataUser::where('id', $lead->data_user_id)->first();
            if($dataUser)
            {
                DataUser::where('id', $dataUser->id)
                            ->update([
                                'lead_status' => $lead->lead_status,
                                'lead_next_follow_up' => $lead->next_follow_up,
                                'lead_last_call' => $lead->last_call,
                                'updated_at' => $dataUser->updated_at,
                            ]);
            }

            //code added on 23 jan 2020. 
            $executive = isset($request['assignTo'])? $request['assignTo']: null;
            if(($request['leadStatus'] == 'no_answer' || $request['leadStatus'] == 'busy' ) && $executive != null && $executive != '')
            {
                $oldExecutive = User::find($lead->assigned_to);
                $newExecutive = User::find($executive);
                $lead->assigned_to = $executive;
                $lead->assigned_type = 'transferred';
                $lead->call_date = date('Y-m-d');
                $lead->update();

                history()->withType('Lead')
                    ->withSubType('assigned')
                    ->withEntity($lead->id)
                    ->withText('trans("history.backend.lead.transferred")')
                    ->withIcon('plus')
                    ->withClass('bg-green')
                    ->withAssets([
                        'user_string' => $newExecutive->name,
                        'olduser_string' => $oldExecutive->name,
                        'date_string' => date('d M y'),
                    ])
                    ->log();
            }
            if($source == 'api') {
                return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => array()]);    
            }
            //  $total_history_count = DB::table('call_history')->select('id')->where('lead_id',$lead->id)->where('data_medium','FBL_REM_MS')->count();
            // $total_history_no_answer_count = DB::table('call_history')->select('id')->where('lead_id',$lead->id)->where('lead_status','no_answer')->where('data_medium','FBL_REM_MS')->count();
            
            // if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
            // {
            //         return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => history()->renderEntity('Lead', $lead->id, null, false)]);
            // }
            // else
            // {
            //     if($lead->data_medium == 'FBL_REM_MS')
            //     {
            //         if($total_history_count == $total_history_no_answer_count)
            //         {
            //             return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => array()]);
            //         }
            //     }
            //     else
            //     {
            //            return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => history()->renderEntity('Lead', $lead->id, null, false)]);
            //     }
            // }
            return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => history()->renderEntity('Lead', $lead->id, null, false)]);
        }
    }

    function fetchCloudCall(CallHistory $callHistory, Request $request){
        $source = $request->get('source');
         //get call recording and details from exotel api
         if($callHistory->exotel_sid != '' && $callHistory->exotel_sid != null) {

            $response = Curl::to('https://'.env('EXOTEL_API_KEY').':'.env('EXOTEL_API_TOKEN').'@'.env('EXOTEL_SUBDOMAIN').'/v1/Accounts/'.env('EXOTEL_SID').'/Calls/'.$callHistory->exotel_sid)
                        ->get();
            $responseXML = json_decode(json_encode(simplexml_load_string($response)));
            
            $callHistory->exotel_call_status = $responseXML->Call->Status;
            if('completed' == $callHistory->exotel_call_status) {
                $callHistory->duration = $responseXML->Call->Duration;
                $callHistory->call_record_file = $responseXML->Call->RecordingUrl;
            }
            $callHistory->update();
        }
        if($source == 'api') {
            return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => array()]);    
        }
        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => history()->renderEntity('Lead', $callHistory->lead_id, null, false)]);
    }

    function cloudCallStatus(CallHistory $callHistory, Request $request){
         //get call recording and details from exotel api
         $callStatus = '';
         if($callHistory->exotel_sid != '' && $callHistory->exotel_sid != null) {

            $response = Curl::to('https://'.env('EXOTEL_API_KEY').':'.env('EXOTEL_API_TOKEN').'@'.env('EXOTEL_SUBDOMAIN').'/v1/Accounts/'.env('EXOTEL_SID').'/Calls/'.$callHistory->exotel_sid)
                        ->get();
            $responseXML = json_decode(json_encode(simplexml_load_string($response)));
            $callStatus = $responseXML->Call->Status;
        }
        
        return response()->json(['Message' => $callStatus, 'Status' => '200']);
    }

    function addLeadNote(Lead $lead, Request $request, LeadRepository $leadRepo)
    {
        $user = $request->user();
        $leadRepo->addLeadNote($lead, $user, $request);

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => history()->renderEntity('Lead', $lead->id, null, false)]);
    }

    function getLeadDetail(Lead $lead, Request $request)
    {
        $data = array(
            'lead_status' => strtoupper($lead->lead_status),
            'status_class' => $lead->status_class,
            'data_medium' => $lead->data_medium,
            'assigned_to_name' => $lead->assigned_executive->name,
            'phone' => $lead->phone,
            'country_code' => $lead->country_code,
        );
        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }

    function deleteAlternateNumber(AlternateNumber $alternateNumber)
    {
        $alternateNumber->delete();
        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    function updateAlternateNumber(AlternateNumber $alternateNumber, Request $request)
    {
        $alternateNumber->name = $request->get('numberName');
        $alternateNumber->relation = $request->get('numberRelation');
        $alternateNumber->save();

        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $alternateNumber]);
    }

    function updateLead(Lead $lead, Request $request)
    {
        $lead->name = $request->get('leadName');
        $lead->relation = $request->get('leadRelation');
        $lead->update();

        $data = array(
            'lead_status' => strtoupper($lead->lead_status),
            'status_class' => $lead->status_class,
            'name' => $lead->name,
            'relation' => $lead->relation,
        );
        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }

    function assignLeadToAnother(Request $request)
    {
        $leadId = $request->get('leadId');
        $executive = $request->get('executive');
        $callDate = $request->get('callDate');

        $lead = Lead::where('id', $leadId)->first();
        if($lead)
        {
            $oldExecutive = User::find($lead->assigned_to);
            $newExecutive = User::find($executive);
            $lead->assigned_to = $executive;
            $lead->lead_status = 'open';
            $lead->assigned_type = 'transferred';
            $lead->call_date = date('Y-m-d', strtotime($callDate));
            $lead->update();

            $dataUser = DataUser::where('id', $lead->data_user_id);
            if($dataUser)
            {
                DataUser::where('id', $dataUser->id)->update(['lead_status' => 'open', 'updated_at' => $dataUser->updated_at]);
            }

            history()->withType('Lead')
                ->withSubType('assigned')
                ->withEntity($lead->id)
                ->withText('trans("history.backend.lead.transferred")')
                ->withIcon('plus')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => $newExecutive->name,
                    'olduser_string' => $oldExecutive->name,
                    'date_string' => date('d M y'),
                ])
                ->log();
            
            return response()->json(['Message' => 'Lead assigned to '.$newExecutive->name, 'Status' => '200']);
        }
        return response()->json(['Message' => 'Lead not found', 'Status' => '422']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listAssignedLeads(Request $request, LeadRepository $leadRepo)
    {
        $user = $request->user();
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                            $query->orWhere('role_id', '2');
                        })
                        ->where('status', '1')
                        ->pluck('name', 'id');

        $cities = $leadRepo->getCitiesFilter();
        $sources = $leadRepo->getMediumFilter();

        return view('backend.lead.assigned', compact('cities', 'sources', 'executives'));
    }

    public function getIncomingDetail()
    {
        $incomingDetails = DB::table('office24responseincoming')
        ->join('leads', 'leads.phone', '=', 'office24responseincoming.callerNumber')
        ->join('users', 'users.id', '=', 'leads.assigned_to')
        ->select('office24responseincoming.call_type','office24responseincoming.user','office24responseincoming.call_time','office24responseincoming.callerNumber','office24responseincoming.call_type', 'leads.assigned_to','leads.name as lead_name','leads.id as lead_id', 'users.name', 'users.email')
        ->where('leads.assigned_to',Auth::id())
       ->first();
       if($incomingDetails)
       {
           $html = '<tr style="cursor:pointer">';
           $html .= '<td><a onclick="incoming()"><span id="lead-dataval" data-val="'.$incomingDetails->lead_id.'" data-name="'.$incomingDetails->lead_name.'">'.$incomingDetails->lead_id.'</span></a></td>';
           $html .= '<td><a onclick="incoming()">'.$incomingDetails->lead_name.'</a></td>';
           $html .= '<td><a onclick="incoming()">'.$incomingDetails->callerNumber.'</a></td>';
           $html .= '<td><a onclick="incoming()">'.$incomingDetails->call_time.'</a></td>';
           $html .= '<td><a onclick="incoming()">'.$incomingDetails->name.'</a></td>';
           $html .= '</tr>';
       }
       else
       {    
           $html = '<tr>';
           $html .= '<td colspan="4"><h4 class="text-center">No Record Found</h4></td>';
           $html .= '</tr>';
       }
      return $html;
    }
}