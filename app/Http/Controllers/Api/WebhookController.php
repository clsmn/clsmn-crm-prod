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
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
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

    //webhook function for office24by7 back response after intiating call.
    function curl_load($url){
            curl_setopt($ch=curl_init(), CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
    }
    function webhookOfficeIncoming(Request $request)
    {
        $insert = DB::table('office24responseincoming')->insert(
                 array(
                        'response'     =>   json_encode($request->all()),
                        'call_type'     =>   $request->get('CALLBACK'),
                        'call_time'     =>   $request->get('CallTime'),
                        'user'     =>   $request->get('User_Name'),
                        'callerNumber'     =>   $request->get('CallerNumber'),
                        'country_code'     =>   $request->get('PhonePrefix'),
                 )
            );
        if($insert)
        {
        return response()->json(['status' => '200']);
        }
        else
        {
        return response()->json(['status' => '201']);
        }
    }

    function webhookOffice(Request $request)
    {
        // sleep(60);
              DB::table('office24response')->insert(
                 array(
                        'response'     =>   json_encode($request->all()),
                        'user'     =>   $request->get('Param_Agent_UserName'),
                        'call_type'     =>   $request->get('Param_Call_Type'),
                        'call_time'     =>   $request->get('Param_Call_Time'),
                        'callerNumber'     =>   $request->get('CallerNumber'),
                 )
            );
        // $content = file_get_contents($request->get('Param_Recording_FilePath'));
        // $json = json_decode(file_get_contents($request->get('Param_Recording_FilePath')));
        $url = $request->get('Param_Recording_FilePath');
        $url = str_replace('\/','/',$url); 
        $headers=get_headers($url);
        $json = stripos($headers[0],"200 OK")?true:false;
        $callRecord = "";
        $file_tempname = "";
        if($json == 1)
        {
           
                $file_tempname = uniqid().rand(10000,99999).'_'.$request->get('Param_Agent_UserName').'_'.$request->get('AgentNumber').'_'.strtotime(date($request->get('Param_Call_Time'))).'.mp3'; 
        //         // // $content = file_get_contents($request->get('Param_Recording_FilePath'));
                Storage::disk('public')->put('call_records/'.$file_tempname, file_get_contents($url));
        }
        // $content = $this->curl_load($url);
        // if(!$this->isHTML($content))
        // {
            // Storage::disk('public')->put('call_records/'.$file_tempname, $content);
        // echo "1111";
        // }
      
        $user = DB::table('users')->select('*')->where('office24by_username',$request->get('Param_Agent_UserName'))->first();
        // echo $request->get('Param_Agent_UserName');
        // print_r($user);
        // die("**");
        $record = CallRecord::where('date', strtotime(date($request->get('Param_Call_Time'))))->where('duration', $request->get('Param_Call_Duration'))->where('user_id', $user->id)->count();
        
        if($request->get('Param_Call_Type') == 'Outgoing')
        {
            if($record == 0)
            {
                // $phone = $request->get('CallerNumber');
                $phone = $request->get('CallerNumber');
                $phoneLength = strlen($phone);
                if($phoneLength == 13)
                {
                    $phone = str_replace('+91', '', $phone);
                }else if($phoneLength == 11)
                {
                    $phone = ltrim($phone, '0');
                }
                if($json == 1)
                {
                    $callRecord = new CallRecord;
                    $callRecord->user_id = $user->id;
                    $callRecord->acrfilename = $file_tempname;
                    $callRecord->date = strtotime(date($request->get('Param_Call_Time')));
                    $callRecord->call_time = $request->get('Param_Call_Time');
                    $callRecord->duration = $request->get('Param_Call_Duration');
                    $callRecord->phone = $phone;
                    $callRecord->save();
                }

              
                //Assign lead
                $leadId = Lead::where('phone', $phone)->value('id');
                if($leadId != null && $leadId != 0)
                {
                    if($json == 1)
                    {
                        $callRecord->lead_id = $leadId;
                        $callRecord->update();
                    }

                    //Assign call record to call history
                    $callHistory = CallHistory::where('called_by', $user->id)
                                ->where('office_ref_id', $request->get('Param_Reference_ID'))
                                // ->where('phone', $phone)
                                // ->where('created_at', $request->get('Param_Call_Time'))
                                ->whereNull('duration')
                                // ->latest()
                                ->first();
                    if($callHistory != null)
                    {

                        $callHistory->duration = $request->get('Param_Call_Duration');
                        $callHistory->call_record_file = $file_tempname;
                        // $callHistory->office_ref_id = $request->get('Param_Reference_ID');
                        $callHistory->office_callType = $request->get('Param_Call_Type');
                        $callHistory->office_username = $request->get('Param_Agent_UserName');
                        $callHistory->office_callTime = $request->get('Param_Call_Time');
                        $callHistory->office_callStatus = $request->get('Param_Call_Status');
                        $callHistory->office_agentStatus = $request->get('Param_Agent_Status');
                        $callHistory->office24by_audioURL = $request->get('Param_Recording_FilePath');
                        $callHistory->call_type = 'sale';
                        $callHistory->lead_status = 'open';
                        $callHistory->saved   = '1';
                        $callHistory->office24by7JSONdump = json_encode($request->all());
                        $callHistory->update();

                        
                        //update callRecord
                        if($json == 1)
                        {
                            $callRecord->call_history_id = $callHistory->id;
                            $callRecord->update();
                        }

                        DB::table('leads')
                        ->where('id', $leadId)  // find your user by their email
                        // ->limit(1)  // optional - to ensure only one record is updated.
                        ->update(array('last_call' => $request->get('Param_Call_Time')));
                        
                    }else{
                        event(new LeadUnattachedCall($callRecord, $user));
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

                }
            }
        }
        elseif($request->get('Param_Call_Type') == 'Incoming')
        {
            // if($record == 0)
            // {
                $phone = $request->get('CallerNumber');
                // $phone = '9893973633';
                $phoneLength = strlen($phone);
                if($phoneLength == 13)
                {
                    $phone = str_replace('+91', '', $phone);
                }else if($phoneLength == 11)
                {
                    $phone = ltrim($phone, '0');
                }
                if($json == 1)
                {
                    $callRecord = new CallRecord;
                    $callRecord->user_id = $user->id;
                    $callRecord->acrfilename = $file_tempname;
                    $callRecord->date = strtotime(date($request->get('Param_Call_Time')));
                    $callRecord->call_time = $request->get('Param_Call_Time');
                    $callRecord->duration = $request->get('Param_Call_Duration');
                    $callRecord->phone = $phone;
                    $callRecord->save();
                }
                // die("****");
                $lastCall = DB::table('call_history')->select('*')->where('phone',$request->get('CallerNumber'))->where('office_username',$request->get('Param_Agent_UserName'))->first();
                $leadId = Lead::where('phone', $phone)->value('id');
                if($lastCall)
                {
                    $callhistory = new CallHistory;
                    $callhistory->lead_id = $lastCall->lead_id;
                    $callhistory->country_code = $lastCall->country_code;
                    $callhistory->phone = $lastCall->phone;
                    $callhistory->called_by = $user->id;
                    $callhistory->call_type = ($lastCall->call_type == '' ? 'sale' : $lastCall->call_type);
                    $callhistory->lead_status = ($lastCall->lead_status == '' ? 'open' : $lastCall->lead_status);
                    $callhistory->data_medium = $lastCall->data_medium;
                    $callhistory->saved = '1';
                    $callhistory->type = $lastCall->type;
                    $callhistory->office_ref_id = $request->get('Param_Call_Ref_ID');

                    $callhistory->save();
                }
                else
                {
                    $data_medium = Lead::where('id', $leadId)->value('data_medium');

                    $callhistory = new CallHistory;
                    $callhistory->lead_id = $leadId;
                    $callhistory->country_code = '+91';
                    $callhistory->phone = $request->get('CallerNumber');
                    $callhistory->called_by = $user->id;
                    $callhistory->call_type = 'sale';
                    $callhistory->lead_status = 'open';
                    $callhistory->data_medium = $data_medium;
                    $callhistory->saved   = '1';
                    $callhistory->office_ref_id = $request->get('Param_Call_Ref_ID');
                    $callCount = CallHistory::where('phone', $phone)->count();
                    if($callCount > 0)
                    {
                        $callhistory->type = 'repeat';
                    }
                    $callhistory->save();
                    // die("2222");
                }
                //Assign lead
                $leadId = Lead::where('phone', $phone)->value('id');
                if($leadId != null && $leadId != 0)
                {
                    // if($json == 1)
                    // {
                        $callRecord->lead_id = $leadId;
                        $callRecord->update();
                    // }

                    //Assign call record to call history
                    $callHistory = CallHistory::where('called_by', $user->id)
                                // ->where('office_ref_id', $request->get->Param_Reference_ID)
                                ->where('phone', $phone)
                                // ->where('created_at', $request->get('Param_Call_Time'))
                                ->whereNull('duration')
                                // ->latest()
                                ->first();
                    // print_r($callHistory);die("****");
                    if($callHistory != null)
                    {
                        // die("96633666993****");
                        // $callHistory->duration = $request->get('Param_Call_Duration');
                        // $callHistory->call_record_file = $file_tempname;
                        // $callHistory->saved = '1';
                        // $callHistory->office_ref_id = $request->get('Param_Call_Ref_ID');
                        // $callHistory->office_callType = $request->get('Param_Call_Type');
                        // $callHistory->office_username = $request->get('Param_Agent_UserName');
                        // $callHistory->office_callTime = $request->get('Param_Call_Time');
                        // $callHistory->office_callStatus = $request->get('Param_Call_Status');
                        // $callHistory->office_agentStatus = $request->get('Param_Agent_Status');
                        // $callHistory->office24by7JSONdump = json_encode($request->all());
                        // $callHistory->office24by_audioURL = $request->get('Param_Recording_FilePath');
                        // $callHistory->update();

                        $update = \DB::table('call_history') 
                                    ->where('id', $callHistory->id) 
                                    ->update( [
                                     'duration' => $request->get('Param_Call_Duration'), 
                                     'call_record_file' => $file_tempname, 
                                     'saved' => 1, 
                                     'office_ref_id' => $request->get('Param_Call_Ref_ID'),
                                     'office_callType' => $request->get('Param_Call_Type'),
                                     'office_username' => $request->get('Param_Agent_UserName'),
                                     'office_callTime' => $request->get('Param_Call_Time'),
                                     'office_callStatus' => $request->get('Param_Call_Status'),
                                     'office_agentStatus' => $request->get('Param_Agent_Status'),
                                     'office24by7JSONdump' => json_encode($request->all()),
                                     'office24by_audioURL' => $request->get('Param_Recording_FilePath'),
                                    
                                 ]); 

                        //update callRecord
                        // if($json == 1)
                        // {
                            $callRecord->call_history_id = $callHistory->id;
                            $callRecord->update();
                        // }

                        
                        DB::table('leads')
                        ->where('id', $leadId)  
                        ->update(array('last_call' => $request->get('Param_Call_Time')));
                        DB::table('history')->insert(
                            array(
                                'type_id' => 4,
                                'sub_type' => 'call',
                                'user_id' => $user->id,
                                'entity_id' => $leadId,
                                'sub_entity_id' => $callHistory->id,
                                'icon' => 'phone',
                                'class' => 'bg-green',
                                'text' => 'trans("history.backend.lead.called")',
                                'assets' => '{"user_string":"'.$user->name.'","date_string":"'.date('d M y').'"}',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            )
                        );

                    }else{
                        event(new LeadUnattachedCall($callRecord, $user));
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

                }
            // }
        }
        else
        {

        }
        return response()->json(['status' => '200']);
    }

    function getaudio(Request $request)
    {
        // $url = DB::select("SELECT `office24by_audioURL`FROM `call_history` WHERE `id` = ".$request->get('id'))->first();
        $url = DB::table('call_history')
                ->select('office24by_audioURL')
                ->where('id',$request->get('id'))
                ->first();
        if($url->office24by_audioURL != "" || $url->office24by_audioURL != null)
        {
            $url = $url->office24by_audioURL;
            $url = str_replace('\/','/',$url); 
            $headers=get_headers($url);
            $json = stripos($headers[0],"200 OK")?true:false;
            $callRecord = "";
            $file_tempname = "";
            if($json == 1)
            {
               
                $file_tempname = uniqid().rand(10000,99999).'.mp3'; 
                Storage::disk('public')->put('call_records/'.$file_tempname, file_get_contents($url));
                DB::table('call_history')
                ->where('id', $request->get('id'))  // find your user by their email
                ->update(array('call_record_file' => $file_tempname));
                return response()->json(['status' => '200', 'data' => 'https://crm.classmonitor.com/storage/call_records/'.$file_tempname]);
            }
            else
            {
                return response()->json(['status' => '201', 'data' => 'Audio not exist.']);
            }
        }
        else
        {
            return response()->json(['status' => '201', 'data' => 'Audio not exist.']);
        }
    }
  function webhookOffice1(Request $request)
    {
        $array = json_decode($request->data);
        $file_tempname = uniqid().rand(10000,99999).$array->Param_Agent_UserName.'_'.$array->Param_Call_Time.'.mp3'; 
        $content = file_get_contents($array->Param_Recording_FilePath);

        Storage::disk('public')->put('call_records/'.$file_tempname, file_get_contents($array->Param_Recording_FilePath));

        // DB::table('office24response')->insert(
        //          array(
        //                 'response'     =>   json_encode($request->all())
        //          )
        //     );
        return response()->json(['status' => '200', 'data' => 'Success']);
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
                
                
                 DB::table('upload_datas')->insert(
                     array(
                            'date'  => date('Y-m-d'),
                            'data_medium'  => $medium,
                            'repeatlLeads'   =>   0,
                            'newLeads'   =>    1,
                            'created_at'   =>   date('Y-m-d H:i:s'),
                            'updated_at'   =>   date('Y-m-d H:i:s'),
                     )
                );
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
                
                DB::table('upload_datas')->insert(
                     array(
                            'date'  => date('Y-m-d'),
                            'data_medium'  => $medium,
                            'repeatlLeads'   =>   1,
                            'newLeads'   =>    0,
                            'created_at'   =>   date('Y-m-d H:i:s'),
                            'updated_at'   =>   date('Y-m-d H:i:s'),
                     )
                );
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
            if($request['phase'] == 'kit_purchased')
            {
                $dataUser->lead_status = 'sale';
            }
            else
            {
                $dataUser->lead_status = NULL;
            }
            $dataUser->update();

            $lead = Lead::where('data_user_id', $dataUser->id)->first();
            $lead->phase = $request['phase'];
            $lead->update();
        }
        return response()->json(['status' => '200', 'data' => 'Ok']);
    }

    function addDataUser(Request $request)
    {
        if($request->name == '')
        {
            $request->name = 'User';
        }
         try {
                $count = DB::table('data_user')->select('*')->where('country_code',$request->countryCode)->where('phone',$request->mobile)->count();
                if($count > 0)
                {
                    return response()->json(['status' => '203', 'data' => 'Already Submitted']);
                }
                $datauser = DB::table('data_user')->insert(
                 array(
                        'name'     =>   $request->name,
                        'email'     =>   $request->email,
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'data_medium' => $request->sourcePage,
                        'lead_from' => $request->lead_from,
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
            if($datauser)
            {
            return response()->json(['status' => '200', 'data' => 'Success']);
            }
            else
            {
            return response()->json(['status' => '201', 'data' => 'some error occured while sending your request. Please try after']);
            }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }

    function addDataUserCPM13(Request $request)
    {
        // print_r($request->all());die("****");
        if($request->name == '')
        {
            $request->name = 'User';
        }
         try {
                $count = DB::table('data_user')->select('*')->where('country_code',$request->countryCode)->where('phone',$request->mobile)->count();
                if($count > 0)
                {
                    return response()->json(['status' => '203', 'data' => 'Already Submitted']);
                }
                $datauser = DB::table('data_user')->insert(
                 array(
                        'name'     =>   $request->name,
                        'email'     =>   $request->email,
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'data_medium' => $request->sourcePage,
                        'lead_from' => $request->lead_from,
                        'child_name' => ($request->child_name == 'undefined' ? '' : $request->child_name),
                        'ageGroup' => ($request->child_age == 'undefined' ? '' : $request->child_age),
                        'message' => ($request->message == 'undefined' ? '' : $request->message),
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
            if($datauser)
            {
            return response()->json(['status' => '200', 'data' => 'Success']);
            }
            else
            {
            return response()->json(['status' => '201', 'data' => 'some error occured while sending your request. Please try after']);
            }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }
   function addDataUserLiveDemo(Request $request)
    {

        if($request->name == '')
        {
            $request->name = 'User';
        }
         try {
                $count = DB::table('data_user')->select('*')->where('country_code',$request->countryCode)->where('phone',$request->mobile)->count();
                if($count > 0)
                {
                    return response()->json(['status' => '203', 'data' => 'Already Submitted']);
                }
                $datauser = DB::table('data_user')->insert(
                 array(
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'ageGroup' => $request->child_age,
                        'data_medium' => $request->sourcePage,
                        'lead_from' => $request->lead_from,
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
            if($datauser)
            {
            return response()->json(['status' => '200', 'data' => 'Success']);
            }
            else
            {
            return response()->json(['status' => '201', 'data' => 'some error occured while sending your request. Please try after']);
            }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }
    function addDataUserGifting(Request $request)
    {
         try {
                $datauser = DB::table('data_user')->insert(
                 array(
                        'name'     =>   'User',
                        'email'     =>   $request->email,
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'ageGroup'     =>   $request->age,
                        'message'     =>   $request->message,
                        'data_medium' => 'gifting_page',
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
                   if($datauser)
        {
        return response()->json(['status' => '200', 'data' => 'Success']);
        }
        else
        {
        return response()->json(['status' => '201', 'data' => 'Error']);
        }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }

    function addDataUserlandingPages(Request $request)
    {
         try {
                $datauser = DB::table('data_user')->insert(
                 array(
                        'name'     =>    $request->name,
                        'email'     =>   $request->email,
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'ageGroup'     =>   $request->age,
                        'data_medium' => $request->source,
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
                   if($datauser)
        {
        return response()->json(['status' => '200', 'data' => 'Success']);
        }
        else
        {
        return response()->json(['status' => '201', 'data' => 'Error']);
        }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }

    function addGoogleLeadExtention(Request $request)
    {
        // print_r($request->all());
        $name = '';
        $countryCode = '';
        $mobile = '';
        $medium = 'LEAD_EXTENSION';
        if($request->google_key == 'uvr]1BX>os(RB]R~O(dTP8Ljb0;8M+')
        {
            foreach($request->user_column_data as $value)
            {
                if($value['column_id'] == 'FULL_NAME')
                {
                    $name = $value['string_value'];
                }
                if($value['column_id'] == 'PHONE_NUMBER')
                {
                 $mobile = $value['string_value'];
                 if($mobile != '' && $mobile != null) 
                 {
                     try 
                     {
                         $number = PhoneNumber::parse($mobile);
                         $cc = $number->getCountryCode();
                         $phoneNumber = $number->getNationalNumber();
                         if($phoneNumber != '') {
                             $mobile = $phoneNumber;
                             $countryCode = '+'.$cc;    
                         }else{
                             $countryCode = '+91';    
                             $rowData['phone'] = substr((int) $mobile, -10);
                         }
                     }
                     catch (PhoneNumberParseException $e) 
                     {
                         $countryCode = '+91';    
                         $mobile = substr((int) $mobile, -10);
                     }

                 }
                }
             
            }

        $webhookURL = 'https://chat.googleapis.com/v1/spaces/AAAAl0HmcSU/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8Awdlmcm58vWp2ZPcz1HPjuxypS1WVvtNigQVqTY1yM%3D';
            Curl::to($webhookURL)
            ->withData( array( 'text' => json_encode($request->all()) ) )
            ->asJson()
            ->post();
        if($mobile == '')
        {
            return response()->json(['status' => '201', 'data' => 'error']);
        }
            $dataUser = DataUser::where('country_code', $countryCode)->where('phone', $mobile)->first();
            if(!$dataUser)
            {
                $dataUser = new DataUser;
                $dataUser->name = $name;
                $dataUser->country_code = $countryCode;
                $dataUser->phone = $mobile;
                $dataUser->status = '';
                $dataUser->data_medium = ($medium)? strtolower($medium) : 'manual';
                $dataUser->moved_to_lead = '0';
                $upload_data = DB::table('upload_datas')->select('*')->where('date', date('Y-m-d'))->where('data_medium', $medium)->get();
                
                
                 DB::table('upload_datas')->insert(
                     array(
                            'date'  => date('Y-m-d'),
                            'data_medium'  => $medium,
                            'repeatlLeads'   =>   0,
                            'newLeads'   =>    1,
                            'created_at'   =>   date('Y-m-d H:i:s'),
                            'updated_at'   =>   date('Y-m-d H:i:s'),
                     )
                );
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
                // $dataUser->phase = ($from == 'cpm_campaign')? '' : $dataUser->phase;
                $dataUser->data_medium = ($medium)? strtolower($medium) : $dataUser->data_medium;
                $dataUser->update();
                $upload_data = DB::table('upload_datas')->select('*')->where('date', date('Y-m-d'))->where('data_medium', $medium)->get();
                
                DB::table('upload_datas')->insert(
                     array(
                            'date'  => date('Y-m-d'),
                            'data_medium'  => $medium,
                            'repeatlLeads'   =>   1,
                            'newLeads'   =>    0,
                            'created_at'   =>   date('Y-m-d H:i:s'),
                            'updated_at'   =>   date('Y-m-d H:i:s'),
                     )
                );
                return response()->json(['status' => '200', 'data' => 'Lead updated.']);
            }

        }

       
    }
    function addDataUserCoach(Request $request)
    {
        if($request->name == '')
        {
            $request->name = 'User';
        }
         try {
                $datauser = DB::table('data_user')->insert(
                 array(
                        'name'     =>   $request->name,
                        'email'     =>   $request->email,
                        'country_code'     =>   $request->countryCode,
                        'phone'     =>   $request->mobile,
                        'data_medium' => 'mom_coach',
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 )
            );
                   if($datauser)
                {
                return response()->json(['status' => '200', 'data' => 'Success']);
                }
                else
                {
                return response()->json(['status' => '201', 'data' => 'Error']);
                }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }
    

   public function updateCallHistory(Request $request)
    {
        $update = \DB::table('call_history') 
            ->where('id', $request->get('history_id')) 
            ->update( [
             'call_type' => $request->get('call_agenda'),
             'lead_status' => $request->get('lead_status'),
             'note' => $request->get('note_update'),
         ]); 

        if($update)
        {
        return response()->json(['status' => '200', 'data' => 'Success']);
        }
        else
        {
        return response()->json(['status' => '201', 'data' => 'Error']);
        }
            
    }
   
   function contactNow(Request $request)
    {
        // print_r($request->all());
        if($request->name == '')
        {
            $request->name = 'User';
        }
         try {
                $array = array(
                        'name'     =>   $request->name,
                        'email'     =>   $request->email,
                        'countryCode'     =>   $request->countryCode,
                        'mobile'     =>   $request->mobile,
                        'message'     =>   $request->message
                 );
             

                $datauser = DB::table('contact_data')->insert($array);
               if($datauser)
                {
                return response()->json(['status' => '200', 'data' => 'Success']);
                }
                else
                {
                return response()->json(['status' => '201', 'data' => 'Error']);
                }
            } catch (Exception $e) {
                print_r($e);
            }

       
    }

    function delightSales(Request $request)
    {
        // print_r($request->all());
     
         try {
                $array = array(
                        'learning_user_id'     =>   $request->learning_user_id,
                        'product_name'     =>   $request->product_name,
                        'client_name'     =>   $request->client_name,
                        'country_code'     =>   $request->country_code,
                        'phone'     =>   $request->phone,
                        'followup_1'     =>   $request->followup_1,
                        'followup_2'     =>   $request->followup_2,
                        'comment'     =>   $request->comment,
                        'created_at'   =>   date('Y-m-d H:i:s'),
                        'updated_at'   =>   date('Y-m-d H:i:s'),
                 );
             

                $datauser = DB::table('delight_sales')->insert($array);
               if($datauser)
                {
                return response()->json(['status' => '200', 'data' => 'Success']);
                }
                else
                {
                return response()->json(['status' => '201', 'data' => 'Error']);
                }
            } catch (Exception $e) {
                return response()->json(['status' => '201', 'data' => $e->message]);
            }

       
    }

    public function zoomMeet(Request $request)
    {
     
        $array = array(
                'dump'     =>   json_encode($request->all()),
         );
        // dd($array);
        $datauser = DB::table('zoomMeet')->insert($array);
       
    }
}