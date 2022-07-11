<?php

namespace App\Http\Controllers;

use App\Models\Data\DataUser;
use App\Models\Data\DataChild;
use App\Models\Lead\CallHistory;
use App\Models\Lead\Lead;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

class TestController extends Controller
{

    public function index()
    {
        dd('ytttt');
        // phpinfo();
        // die;

        try {
            $number = PhoneNumber::parse('9074251626');
            echo $number->getRegionCode().'-'; // GB
            echo $number->getCountryCode().'-'; // 44
            echo $number->getNationalNumber();
        }
        catch (PhoneNumberParseException $e) {
            // 'The string supplied is too short to be a phone number.'
        }

        dd('ytttt');

        $callhistory = CallHistory::with('lead')->where('data_medium', '')->get();
        foreach($callhistory as $call)
        {
            dd($call->lead->data_medium);
            CallHistory::where('id', $call->id)
                        ->update([
                            'data_medium' => $call->lead->data_medium
                        ]);
        }

        dd('tests');

         // foreach($leads as $lead)
        // {
        //     $call = CallHistory::where('lead_id', $lead->id)
        //                         ->where('saved', '1')
        //                         ->orderBy('updated_at', 'DESC')
        //                         ->first();
        //     if($call)
        //     {
        //         if($call->lead_status == $lead->lead_status)
        //         {
        //             $lead->lead_status_at = $call->updated_at;
        //             $lead->update();
        //         }
        //     }
        // }
        
        // $leads = Lead::where('assigned_to', '13')->get();
        // foreach($leads as $lead)
        // {
        //     $lead->assigned_to = 0;
        //     $lead->update();

        //     $dataLead = DataUser::find($lead->data_user_id);
        //     if($dataLead)
        //     {
        //         $dataLead->moved_to_lead = '0';
        //         $dataLead->update();
        //     }
        // }
        // dd($leads);

        // $users = DataUser::where('moved_to_lead', '1')->whereNull('assigned_to')->get();
        // foreach($users as $user)
        // {
        //     $lead = Lead::where('country_code', $user->country_code)->where('phone', $user->phone)->first();
        //     if($lead)
        //     {
        //         $user->assigned_to = $lead->assigned_to;
                
        //     }else{
        //         $user->moved_to_lead = '0';
        //         $user->assigned_to = NULL;
        //     }
        //     $user->update();
        // }
        
        dd('test');

        // $response = Curl::to('https://hooks.slack.com/services/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')
        // ->withData( array( 'text' => 'tset set set set set e' ) )
        // ->asJson()
        // ->post();

        $response = Curl::to('http://startwith.us/api/subscription')
                ->withData( array( 'phone' => '9074251626', 'country_code' => '+91', 'learning_id' => '1', 'subscription_type' => 'PAID' ) )
                ->asJson()
                ->post();
        dd($response);

        /*
        // Code for lead transfer
        ---------------------------------------------------------------------------------
        
        $leads = Lead::where('assigned_to', '9')->where('lead_status', 'mild')->get();
        foreach($leads as $lead)
        {
            $lead->assigned_to = '5';
            $lead->assigned_type = 'transferred';
            $lead->update();

            history()->withType('Lead')
                ->withSubType('assigned')
                ->withEntity($lead->id)
                ->withText('trans("history.backend.lead.transferred")')
                ->withIcon('plus')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => 'Chanchala',
                    'olduser_string' => 'Pragya',
                    'date_string' => date('d M y'),
                ])
                ->log();
        }
        dd('test');
        /*
        ----------------------------------------------------------------------------------
        */

        // $schools = DataChild::groupBy('school_id')->pluck('school_id');
        // foreach($schools as $school)
        // {
        //     $schoolData = DB::connection('messenger')
        //                         ->table('cm_schools as s')
        //                         ->leftJoin('cm_cities as c', 'c.id', '=', 's.city')
        //                         ->leftJoin('cm_states as st', 'st.id', '=', 's.state')
        //                         ->select('st.state_name', 'c.city_name', 's.branch_name', 's.school_name')
        //                         ->where('s.id', $school)
        //                         ->first();
        //     if($schoolData != null)
        //     {
        //         DataChild::where('school_id', $school)->update([
        //             'school_name' => $schoolData->school_name,
        //             'school_branch' => $schoolData->branch_name,
        //             'school_city' => $schoolData->city_name,
        //             'school_state' => $schoolData->state_name
        //         ]);
        //     }
            
        // }

        // $schools = DataChild::whereNotNull('school_id')->where('school_id', '!=', '0')->groupBy('school_id')->pluck('school_id');
        // foreach($schools as $school)
        // {
        //     $schoolData = DB::connection('messenger')
        //                         ->table('cm_schools as s')
        //                         ->leftJoin('cm_cities as c', 'c.id', '=', 's.city')
        //                         ->leftJoin('cm_states as st', 'st.id', '=', 's.state')
        //                         ->select('st.state_name', 'c.city_name')
        //                         ->where('s.id', $school)
        //                         ->first();
        //     $users = DataChild::where('school_id', $school)->pluck('data_user_id');
        //     foreach($users as $user)
        //     {
        //         $user = DataUser::find($user);
        //         if($user != null)
        //         {
        //             if($user->city == null || $user->city == '')
        //             {
        //                 $user->city = $schoolData->city_name;
        //             }
        //             if($user->state == null || $user->state == '')
        //             {
        //                 $user->state = $schoolData->state_name;
        //             }
        //             $user->update();
        //         }
        //     }
        // }
        
        dd('test');
        // $rows = DataUser::whereNull('lead_stage')->get();
        // dd($rows->count());
        
        // foreach($rows as $lead)
        // {
        //     $loginServerUser =  DB::connection('login')
        //                         ->table(config('table.login.users'))
        //                         ->where('phone', $lead->phone)
        //                         ->first();

        //     if($loginServerUser != null)
        //     {
        //         $lead->login_id = $loginServerUser->id;
        //         $lead->learning = $loginServerUser->learning;
        //         $lead->learning_id = $loginServerUser->learning_id;
        //         $lead->update();
        //     }

        //     $leadStage = '1';
        //     if($lead->learning != '0' && $lead->learning_id != '0')
        //     {
        //         $subscriptions = DB::connection('learning')
        //                     ->table(config('table.learning.subscriptions').' as s')
        //                     ->where('s.user_id', $lead->learning_id)
        //                     ->where('s.subscription_status', 'ACTIVE')
        //                     ->select('s.alias', 's.subscription_type')
        //                     ->get();
        //         $leadStage = '2';
        //         if($subscriptions->count() > 0)
        //         {
        //             $leadStage = '3';
        //             if($subscriptions->where('subscription_type', 'PAID')->count() > 0)
        //             {
        //                 $leadStage = '4';
        //             }
        //         }
        //     }
        //     $lead->lead_stage = $leadStage;
        //     $lead->update();
        // }
        dd('test');
        $classes = DB::connection('messenger')
                        ->table(config('table.messenger.class').' as c')
                        ->leftJoin(config('table.messenger.school').' as s', 'c.school_id', '=', 's.id')
                        ->whereIn('c.class_grade', [1,2,3,4])
                        ->select('s.school_name', 'c.school_id', 'c.name', 'c.class_grade', 'c.id')
                        ->get();

        //loop trough classes
        foreach($classes as $class)
        {
            //get students with move_to_crm=0
            $students = DB::connection('messenger')
                        ->table(config('table.messenger.children').' as c')
                        ->leftJoin(config('table.messenger.users').' as u', 'c.parent_id', '=', 'u.user_id')
                        ->where('c.class_id', $class->id)
                        ->where('c.move_to_crm', '0')
                        ->where('u.country_code', '+91')
                        ->select('c.id as student_id', 'c.name as student_name', 'c.dob', 'c.status as student_status', 'c.added_on', 
                        'u.user_id', 'u.name', 'u.email', 'u.country_code', 'u.phone_no', 'u.registered_on', 'u.last_activity', 'u.status', 'u.user_lat_long',
                        'u.user_country', 'u.user_state', 'u.user_city', 'u.user_locality', 'u.verified_email')
                        ->get();
            foreach($students as $row)
            {
                //check user in data_user add user if not exist
                $user = DataUser::where('country_code', $row->country_code)
                                ->where('phone', $row->phone_no)
                                ->first();
                if($user == null)
                {
                    //create user
                    $user = new DataUser;
                    $user->name = $row->name;
                    $user->email = $row->email;
                    $user->country_code = $row->country_code;
                    $user->phone = $row->phone_no;
                    $user->messenger_id = $row->user_id;
                    $user->status = $row->status;
                    $user->lat_long = $row->user_lat_long;
                    $user->locality = $row->user_locality;
                    $user->city = $row->user_city;
                    $user->state = $row->user_state;
                    $user->country = $row->user_country;
                    $user->data_medium = 'messenger';
                    $user->email_verified = $row->verified_email;
                    $user->last_activity = ($row->last_activity == '0000-00-00 00:00:00')? null : $row->last_activity;
                    $user->registered_on = $row->registered_on;
                    $user->save();
                }

                //check child
                $child = DataChild::where('student_id', $row->student_id)->first();

                if($child == null)
                {
                    //add child
                    $child = new DataChild;
                    $child->data_user_id = $user->id;
                    $child->student_id = $row->student_id;
                    $child->name = $row->student_name;
                    $child->dob = ($row->dob == '0000-00-00')? null : $row->dob;
                    $child->grade = $class->class_grade;
                    $child->class_id = $class->id;
                    $child->class_name = $class->name;
                    $child->school_id = $class->school_id;
                    $child->school_name = $class->school_name;
                    $child->status = $row->student_status;
                    $child->data_medium = 'messenger';
                    $child->added_on = $row->added_on;
                    $child->save();
                }

                //update move_to_crm to 1
                DB::connection('messenger')
                    ->table(config('table.messenger.children'))
                    ->where('id', $row->student_id)
                    ->update(['move_to_crm' => '1']);

            }
        }

    }

    function test1()
    {
        dd('test');
        $children = DB::connection('login')
                        ->table(config('table.login.children').' as c')
                        ->leftJoin(config('table.login.users').' as u', 'c.user_id', '=', 'u.id')
                        ->whereIn('c.child_class', ['1','2','3','4','18'])
                        ->where('c.move_to_crm', '0')
                        ->select('c.id as student_id', 'c.name as student_name', 'c.child_class as grade', 'c.dob', 'c.gender', 'c.status as student_status', 'c.class_id', 'c.school_id', 'u.id as user_id', 'u.name', 'u.email', 'u.email_verified', 'u.country_code', 'u.phone', 'u.learning', 'u.community', 'u.messenger', 'u.learning_id', 'u.community_id', 'u.parent_id', 'u.teacher_id', 'u.lat_long', 'u.city', 'u.state', 'u.country', 'u.locality', 'u.last_activity', 'u.status', 'u.created_at')
                        ->get();

        if($children)
        {
            foreach($children as $row)
            {
                if($row->user_id != '' && $row->user_id != NULL)
                {
                    $user = DataUser::where('country_code', $row->country_code)
                                    ->where('phone', $row->phone)
                                    ->first();
                    if($user == null)
                    {
                        //create user
                        $user = new DataUser;
                        $user->name = $row->name;
                        $user->email = $row->email;
                        $user->country_code = $row->country_code;
                        $user->phone = $row->phone;
                        $user->messenger = $row->messenger;
                        $user->messenger_id = $row->parent_id;
                        $user->learning = $row->learning;
                        $user->learning_id = $row->learning_id;
                        $user->community = $row->community;
                        $user->community_id = $row->community_id;
                        $user->login_id = $row->user_id;
                        $user->status = $row->status;
                        $user->lat_long = $row->lat_long;
                        $user->locality = $row->locality;
                        $user->city = $row->city;
                        $user->state = $row->state;
                        $user->country = $row->country;
                        $user->data_medium = 'login';
                        $user->email_verified = $row->email_verified;
                        $user->last_activity = ($row->last_activity == '0000-00-00 00:00:00')? null : $row->last_activity;
                        $user->registered_on = $row->created_at;
                        $user->save();
                    }else{
                        //update user
                        $user->name = $row->name;
                        $user->email = $row->email;
                        $user->messenger = $row->messenger;
                        $user->messenger_id = $row->parent_id;
                        $user->learning = $row->learning;
                        $user->learning_id = $row->learning_id;
                        $user->community = $row->community;
                        $user->community_id = $row->community_id;
                        $user->login_id = $row->user_id;
                        $user->status = $row->status;
                        $user->lat_long = $row->lat_long;
                        $user->locality = $row->locality;
                        $user->city = $row->city;
                        $user->state = $row->state;
                        $user->country = $row->country;
                        $user->email_verified = $row->email_verified;
                        $user->last_activity = ($row->last_activity == '0000-00-00 00:00:00')? null : $row->last_activity;
                        $user->update();
                    }

                    //check child
                    $childQuery = DataChild::where('data_user_id', $user->id);
                    if($row->class_id != 0 && $row->school_id != 0)
                    {
                        $childQuery->where('class_id', $row->class_id);
                        $childQuery->where('school_id', $row->school_id);
                    }
                    $childQuery->where('name', $row->student_name);
                    $child = $childQuery->first();

                    if($child == null)
                    {
                        //add child
                        $child = new DataChild;
                        $child->data_user_id = $user->id;
                        $child->student_id = $row->student_id;
                        $child->name = $row->student_name;
                        $child->dob = ($row->dob == '0000-00-00')? null : $row->dob;
                        $child->grade = $row->grade;
                        $child->gender = $row->gender;
                        $child->class_id = $row->class_id;
                        $child->class_name = '';
                        $child->school_id = $row->school_id;
                        $child->school_name = '';
                        $child->status = $row->student_status;
                        $child->data_medium = 'login';
                        $child->added_on = NULL;
                        $child->save();
                    }
                }

                //update move_to_crm to 1
                DB::connection('login')
                        ->table(config('table.login.children'))
                        ->where('id', $row->student_id)
                        ->update([
                            'move_to_crm' => '1'
                        ]);
            }
        }
    }

    function phaseUpdate()
    {
        $users = DataUser::where('phase_updated', '0')->where('moved_to_lead', '1')->whereNotNull('phase')->orderBy('created_at', 'DESC')->limit(10000)->get();
        foreach($users as $user) {
            Lead::where('data_user_id', $user->id)->update(['phase' => $user->phase]);

            DataUser::where('id', $user->id)
                    ->update(['updated_at' => $user->updated_at, 'phase_updated' => '1']);
        }
        dd('done');
    }

    function dataBankUpdate()
    {
        $users = DataUser::where('phase_updated', '2')
                    ->whereNull('lead_last_call')
                    ->whereDate('created_at', '>=', '2018-01-01')
                    ->whereDate('created_at', '<=', '2018-12-31')
                    ->limit(500)
                    ->get();
        // dd($users);
        foreach($users as $row) {

            DataUser::where('id', $row->id)
                    ->update(['updated_at' => $row->created_at, 'phase_updated' => '2']);
        }
        dd('done');
    }

    function noAnswerExport()
    {
        $fileName = 'JAN_PU.csv';
        $leads = Lead::where('lead_status', 'no_answer')->where(function($query){
            $query->where('data_medium', 'JAN_PU');
            // $query->orWhere('data_medium', 'La_n_q2');
        })
        ->select([
            'id',
            'data_user_id',
        ])
        ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('name', 'country_code', 'phone', 'city');

        $callback = function() use($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                //check for no answer count
                $totalCalls = $lead->calls()->where('saved', '1')->count();
                $totalNoAnswerCalls = $lead->calls()->where('saved', '1')->where(function($query) {
                    $query->where('lead_status', 'no_answer');
                    // $query->orWhere('lead_status', 'hot');
                })->count();

                if($totalCalls == $totalNoAnswerCalls) {
                    $dataUser  = DataUser::findOrFail($lead->data_user_id);

                    $row['name']            = $dataUser->name;
                    $row['country_code']    = $dataUser->country_code;
                    $row['phone']           = $dataUser->phone;
                    $row['city']            = $dataUser->city;

                    fputcsv($file, array($row['name'], $row['country_code'], $row['phone'], $row['city']));
                }
                
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    function webhookLead()
    {
        if(isset($_REQUEST['hub_verify_token'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $verify_token = $_REQUEST['hub_verify_token'];

            if ($verify_token === 'abc123') {
                echo $challenge;
            }
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $webhookURL = 'https://chat.googleapis.com/v1/spaces/AAAAl0HmcSU/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8Awdlmcm58vWp2ZPcz1HPjuxypS1WVvtNigQVqTY1yM%3D';
        Curl::to($webhookURL)
            ->withData( array( 'text' => json_encode($input) ) )
            ->asJson()
            ->post();
        // $input = json_decode(file_get_contents('php://input'), true);
        error_log(print_r($input, true));

        // return http_response_code(200);
    }

    function callHistoryClone()
    {

        $query = DB::table('leads as l')
                        ->leftJoin('call_history_data', 'call_history_data.id', 'l.last_call_id')
                        ->leftJoin('users as u1', 'u1.id', 'call_history_data.called_by')
                        ->leftJoin('users as u2', 'u2.id', 'l.assigned_to')
                        ->select(['l.id as lead_id', 'l.data_user_id', 'l.country_code', 'l.phone', 'l.name', 'l.data_medium', 'l.last_call', 'l.next_follow_up', 'l.lead_status', 'l.assigned_to', 
                        'call_history_data.id', 'call_history_data.exotel_sid','call_history_data.duration', 'call_history_data.call_record_file', 'call_history_data.called_by', 'call_history_data.created_at',
                        'u1.name as called_by_name', 'u2.name as assigned_to_name', 'l.frequency']);

        $query->orderBy(config('access.call_history_data_table').'.created_at','DESC');

        $ss =$query->count();
                        
        dd($ss);
        // $query = CallHistory::where(config('access.call_history_table').'.saved', '1');
        // $query->whereIn(config('access.call_history_table').'.id', function($q){
        //     $q->select(DB::raw('MAX(id) FROM '.config('access.call_history_table').' WHERE saved="1" GROUP BY lead_id'));
        // });
        // $query->groupBy(config('access.call_history_table').'.lead_id');
        // $query->orderBy(config('access.call_history_table').'.created_at', 'DESC');

        // $Oldleads = $query->get();
        // $i = 1;
        // foreach($Oldleads as $callHistory)
        // {
        //     $values = array(
        //             'lead_id' => $callHistory->lead_id,
        //             'country_code' => $callHistory->country_code,
        //             'phone' => $callHistory->phone,
        //             'called_by' => $callHistory->called_by,
        //             'duration' => $callHistory->duration,
        //             'call_type' => $callHistory->call_type,
        //             'call_record_file' => $callHistory->call_record_file,
        //             'note' => $callHistory->note,
        //             'lead_status' => $callHistory->lead_status,
        //             'schedule_address' => $callHistory->schedule_address,
        //             'schedule_time' => $callHistory->schedule_time,
        //             'next_follow_up' => $callHistory->next_follow_up,
        //             'type' => $callHistory->type,
        //             'saved' => $callHistory->saved,
        //             'created_at' => $callHistory->created_at,
        //             'updated_at' => $callHistory->updated_at,
        //             'exotel_sid' => $callHistory->exotel_sid,
        //             'exotel_call_status' => $callHistory->exotel_call_status,
        //             'data_medium' => $callHistory->data_medium,
        //         );
        //         DB::table('call_history_data')->insert($values);
        //     echo $i.'. Done: '.$callHistory->id.'</br>';
        //     $i++;

        // }
    }
}
