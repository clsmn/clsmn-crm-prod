<?php
namespace App\Http\Controllers\Api;

use Storage;
use App\Models\Lead\Lead;
use Illuminate\Http\Request;
use App\Models\Data\DataUser;
use Ixudra\Curl\Facades\Curl;
use App\Models\Lead\CallRecord;
use App\Models\Lead\CallHistory;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Events\Backend\Lead\LeadUnattachedCall;

class WebhookController extends Controller
{
    function acrWebhook(User $user, Request $request)
    {
        // $msg = 'ACR Webhook:';
        // $msg .= (string) json_encode($request->all());
        // $msg .= (string) json_encode($user);

        // $response = Curl::to('https://hooks.slack.com/services/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')
        //             ->withData( array( 'text' => $msg ) )
        //             ->asJson()
        //             ->post();

        if($request->hasFile('file'))
        {
            $tmpName    = \Illuminate\Support\Facades\Input::file('file')->getPathName();
            Storage::disk('public')->put('call_records/'.$request['acrfilename'], file_get_contents($tmpName));
        }

        //check Existing call record
        $record = CallRecord::where('date', $request['date'])->where('duration', $request['duration'])->where('user_id', $user->id)->count();
        if($record == 0)
        {
            $phone = $request['phone'];
            $phoneLength = strlen($phone);
            if($phoneLength == 13)
            {
                $phone = str_replace('+91', '', $phone);
            }else if($phoneLength == 11)
            {
                $phone = ltrim($phone, '0');
            }

            $callRecord = new CallRecord;
            $callRecord->user_id = $user->id;
            $callRecord->acrfilename = $request['acrfilename'];
            $callRecord->date = $request['date'];
            $callRecord->call_time = date('Y-m-d H:i:s', ($request['date'] + 19800));
            $callRecord->duration = $request['duration'];
            $callRecord->phone = $phone;
            $callRecord->save();

            //Assign lead
            $leadId = Lead::where('phone', $phone)->value('id');
            if($leadId != null && $leadId != 0)
            {
                $callRecord->lead_id = $leadId;
                $callRecord->update();

                //Assign call record to call history
                $callHistory = CallHistory::where('called_by', $user->id)
                            ->where('phone', $phone)
                            ->whereNull('duration')
                            ->whereDate('created_at', date('Y-m-d', ($request['date'] + 19800)))
                            ->latest()
                            ->first();

                if($callHistory != null)
                {
                    $callHistory->duration = $request['duration'];
                    $callHistory->call_record_file = $request['acrfilename'];
                    $callHistory->update();

                    //update callRecord
                    $callRecord->call_history_id = $callHistory->id;
                    $callRecord->update();
                }else{
                    //event(new LeadUnattachedCall($callRecord, $user));
                    history()->withType('Lead')
                            ->withSubType('call_record')
                            ->withEntity($leadId)
                            ->withSubEntity($callRecord->id)
                            ->withUserId($user->id)
                            ->withText('call unattached')
                            ->withIcon('phone')
                            ->withClass('bg-green')
                            ->log();
                }

                // Add history for unattached call record
                // if($callRecord->lead_id != null && $callRecord->call_history_id == null)
                // {
                
                // }
            }
        }
    }

    function addLead(Request $request)
    {
        $msg = 'Lead Webhook:';
        $msg .= (string) json_encode($request->all());

        $response = Curl::to('https://hooks.slack.com/services/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')
                    ->withData( array( 'text' => $msg ) )
                    ->asJson()
                    ->post();

        $this->validate($request, [
            'phone' => 'required|size:10',
            'country_code' => 'required'
        ]);
        
        $medium = $request->get('source');
        $from = $request->get('from');
        $name = $request->get('name');
        $phone = $request->get('phone');
        $countryCode = $request->get('country_code');
        // insert into data bank
        // if($countryCode == '+91')
        // {
            $dataUser = DataUser::where('country_code', $countryCode)->where('phone', $phone)->first();
            if(!$dataUser)
            {
                $dataUser = new DataUser;
                $dataUser->name = ($name)? $name: 'User';
                $dataUser->country_code = $countryCode;
                $dataUser->phone = $request->get('phone');
                $dataUser->status = '';
                $dataUser->data_medium = ($medium)? strtolower($medium) : 'manual';
                $dataUser->moved_to_lead = '0';
                $upload_data = DB::table('upload_datas')->select('*')->where('date', date('Y-m-d'))->where('data_medium', $medium)->get();
                
                
                // DB::table('upload_datas')
                //             ->where('date', date('Y-m-d'))  
                //             ->where('data_medium', $medium)  
                //             ->update(
                //                 array(
                //                         'repeatlLeads'   =>   $upload_data[0]->repeatlLeads + 1,
                //                         'created_at'   =>   date('Y-m-d h:m:s'),
                //                         'updated_at'   =>   date('Y-m-d h:m:s'),
                //                       )
                //             );
                return DB::transaction(function() use($dataUser, $request){
                    if($dataUser->save())
                    {
                        //update lead
                        //check on messenger server
                        $loginUser = DB::connection('login')
                                ->table(config('table.login.users'))
                                ->where('country_code', $dataUser->country_code)
                                ->where('phone', $dataUser->phone)
                                ->first();
                        if($loginUser != null)
                        {
                            $dataUser->messenger        = $loginUser->messenger;
                            $dataUser->messenger_id     = $loginUser->parent_id;
                            $dataUser->learning         = $loginUser->learning;
                            $dataUser->learning_id      = $loginUser->learning_id;
                            $dataUser->community        = $loginUser->community;
                            $dataUser->community_id     = $loginUser->community_id;
                            $dataUser->login_id         = $loginUser->id;
                            $dataUser->status           = $loginUser->status;
                            $dataUser->update();
                        }
        
                        return response()->json(['status' => '200', 'data' => 'Lead added.']);
                    }
                    return response()->json(['status' => '422', 'data' => 'Some error occurred try again later.']);
                });
            }
            else
            {
                $dataUser->phase = ($from == 'cpm_campaign')? '' : $dataUser->phase;
                $dataUser->data_medium = ($medium)? strtolower($medium) : $dataUser->data_medium;
                $dataUser->update();
                $upload_data = DB::table('upload_datas')->select('*')->where('date', date('Y-m-d'))->where('data_medium', $medium)->get();
                
                // DB::table('upload_datas')
                //             ->where('date', date('Y-m-d'))  
                //             ->where('data_medium', $medium)  
                //             ->update(
                //                 array(
                //                         'newLeads'   =>   $upload_data[0]->newLeads + 1,
                //                       )
                //     );
                return response()->json(['status' => '200', 'data' => 'Lead updated.']);
            }
        // }
        return response()->json(['status' => '422', 'data' => 'Some error occurred try again later.']);
    }

    function subscriptionPurchased(Request $request)
    {
        $msg = 'Subscription Webhook:';
        $msg .= (string) json_encode($request->all());

        Curl::to('https://hooks.slack.com/services/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')
                    ->withData( array( 'text' => $msg ) )
                    ->asJson()
                    ->post();

        $this->validate($request, [
            'phone' => 'required|size:10',
            'country_code' => 'required',
            'learning_id' => 'required',
            'subscription_type' => 'required'
        ]);
        $dataUser = DataUser::where('country_code', $request['country_code'])->where('phone', $request['phone'])->first();
        if($dataUser)
        {
            $dataUser->learning = '1';
            $dataUser->learning_id = $request['learning_id'];
            $subscriptionType = $request['subscription_type'];
            if($subscriptionType == 'FREE')
            {
                $dataUser->phase = 'trial';
            }else{
                $dataUser->phase = 'kit_purchased';
                $dataUser->lead_status = 'sale';
            }
            if($dataUser->subscription_type == NULL)
            {
                $dataUser->subscription_type = $subscriptionType;
            }else if($dataUser->subscription_type == 'FREE' && $subscriptionType == 'PAID')
            {
                $dataUser->subscription_type = 'PAID';
            }
            // $dataUser->lead_status = NULL;
            $dataUser->update();

            $lead = Lead::where('data_user_id', $dataUser->id)->first();
            $lead->phase =  $dataUser->phase;
            $lead->update();
        }
        return response()->json(['status' => '200', 'data' => 'Ok']);
    }

    function updateLeadPhase(Request $request)
    {
        $msg = 'Lead Phase Webhook:';
        $msg .= (string) json_encode($request->all());

        Curl::to('https://hooks.slack.com/services/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')
                    ->withData( array( 'text' => $msg ) )
                    ->asJson()
                    ->post();

        $this->validate($request, [
            'phone'         => 'required|size:10',
            'country_code'  => 'required',
            'phase'         => 'required',
        ]);
        $dataUser = DataUser::where('country_code', $request['country_code'])->where('phone', $request['phone'])->first();
        if($dataUser)
        {
            $dataUser->phase = $request['phase'];
            $dataUser->lead_status = NULL;
            $dataUser->update();

            $lead = Lead::where('data_user_id', $dataUser->id)->first();
            $lead->phase = $request['phase'];
            $lead->update();
        }
        return response()->json(['status' => '200', 'data' => 'Ok']);
    }
}