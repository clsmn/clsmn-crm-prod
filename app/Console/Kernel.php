<?php

namespace App\Console;

use App\Models\Lead\Lead;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\Data\DataUser;
use App\Models\Data\DataChild;
use App\Models\Access\User\User;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Events\Backend\Lead\LeadAssigned;
use Ixudra\Curl\Facades\Curl;
use Hash;
use App\Models\DailyTrack;
use Illuminate\Http\Request;
use App\Models\Lead\CallHistory;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Lead\LeadRepository;

use Redirect;
use Auth;
use Mail;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];
 
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $date = date('Y-m-d');
            Lead::whereDate('next_follow_up', $date)->update(['call_date' => $date, 'done' => '0']);
        })->dailyAt('3:00');

        $webhookURL = 'https://chat.googleapis.com/v1/spaces/AAAAl0HmcSU/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8Awdlmcm58vWp2ZPcz1HPjuxypS1WVvtNigQVqTY1yM%3D';

        $schedule->call('App\Repositories\Backend\Sales\DelightSalesRepository@assignSales')
            ->daily()
            ->before(function () use($webhookURL) {
                Curl::to($webhookURL)
                    ->withData( array( 'text' => 'Cron Start: Delight Team sales assignment starts' ) )
                    ->asJson()
                    ->post();
            })
            ->after(function () use($webhookURL) {
                Curl::to($webhookURL)
                ->withData( array( 'text' => 'Cron End: Delight Team sales assignment ends' ) )
                ->asJson()
                ->post();
            });

        // $schedule->call('App\Http\Controllers\TestController@index')->everyMinute()->name('callHistory')->withoutOverlapping();;

        // $schedule->call(function () {
            

        //     $startDate = date("Y-m-d", strtotime("-29 day"));

        //     $Deliteleads = DB::table('subscriptions')->whereDate('created_at', '=', $startDate)->where('subscription_type', 'PAID')->orderBy('created_at','ASC')->pluck('datauser_id');

        //     $dataUserId = "";
        //     $executive = 140;
        //     $callDate = date('Y/m/d');
        //     $assignToMass = 'true';
        //     $ids = $Deliteleads;

        //     $arr = explode('/', $callDate);
        //     $callDate = $arr[2].'-'.$arr[1].'-'.$arr[0];

        //     $user = User::findOrFail($executive);

        //     DB::transaction(function () use ($user, $dataUserId, $callDate, $assignToMass, $ids) {
                
        //         $data = DataUser::whereIn('id', $ids)->get();    
        //         if($data->count() > 0)
        //         {
        //             foreach($data as $row)
        //             {
        //                 DataUser::where('id', $row->id)->update(['moved_to_lead' => '1', 'lead_status' => 'open', 'assigned_to' => $user->id, 'updated_at' => $row->updated_at]);
        //                 $lead = Lead::where('country_code', $row->country_code)->where('phone', $row->phone)->first();
        //                 if(!$lead)
        //                 {
        //                     $lead = new Lead;
        //                     $lead->data_user_id      = $row->id;
        //                     $lead->name              = $row->name;
        //                     $lead->email             = $row->email;
        //                     $lead->country_code      = $row->country_code;
        //                     $lead->phone             = $row->phone;
        //                     $lead->messenger         = $row->messenger;
        //                     $lead->messenger_id      = $row->messenger_id;
        //                     $lead->learning          = $row->learning;
        //                     $lead->learning_id       = $row->learning_id;
        //                     $lead->community         = $row->community;
        //                     $lead->community_id      = $row->community_id;
        //                     $lead->login_id          = $row->login_id;
        //                     $lead->status            = $row->status;
        //                     $lead->lat_long          = $row->lat_long;
        //                     $lead->locality          = $row->locality;
        //                     $lead->city              = $row->city;
        //                     $lead->state             = $row->state;
        //                     $lead->country           = $row->country;
        //                     $lead->data_medium       = $row->data_medium;
        //                     $lead->email_verified    = $row->email_verified;
        //                     $lead->last_activity     = $row->last_activity;
        //                     $lead->registered_on     = $row->registered_on;
        //                     $lead->assigned_to       = $user->id;
        //                     $lead->lead_stage        = $row->lead_stage;
        //                     $lead->call_date         = $callDate;
        //                     $lead->save();
        
        //                     event(new LeadAssigned($lead, $user));
        //                 }else{
        //                     $oldUser = User::find($lead->assigned_to);

        //                     $lead->done = '0';
        //                     $lead->assigned_to = $user->id;
        //                     $lead->lead_status = 'open';
        //                     $lead->assigned_type = 'transferred';
        //                     $lead->update();
        //                 }
        //             }

        //             return true;
        //         }
        //     });
    
        // })->dailyAt('2:00');
       
       $schedule->call(function () {
            $data = DB::table('upload_datas')
                    ->where('date',date('Y-m-d', strtotime("-1 day")))
                    ->groupBy('data_medium')
                    ->select('data_medium as source')
                    ->get();

            $i = 0;
            $j = 0;
            $total_leads = 0;
            $total_repeat_leads = 0;
            $total__new_leads = 0;
            $html = '<thead>
                    <tr>
                        <th>Source</th>
                        <th>Total Leads</th>
                        <th>Repeat Leads</th>
                        <th>New Leads</th>
                    </tr>
                </thead>
                <tbody id="" >';
            if($data)
            {
            foreach($data as $value)
            {

                 $lead_perform = \DB::table("upload_datas")
                          ->select('data_medium',
                                    DB::raw("(SELECT count(id) FROM upload_datas
                                          WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d' , strtotime("-1 day"))."' 
                                        ) as total_source"),
                                   DB::raw("(SELECT SUM(repeatlLeads) FROM upload_datas
                                          WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d' , strtotime("-1 day"))."' 
                                        ) as repeatlLeads"),
                                   DB::raw("(SELECT SUM(newLeads) FROM upload_datas
                                          WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d' , strtotime("-1 day"))."' 
                                        ) as newLeads")
                                  
                                  )
                          ->first();
                 // print_r($lead_perform);die();      
                $variable = "'".$value->source."','".date('Y-m-d', strtotime("-1 day"))."','".date('Y-m-d', strtotime("-1 day"))."'";   
                $html .= '<tr class="" id="source_id_'.$value->source.'">';
                $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$value->source.'</a></td>';
                if($lead_perform->total_source > 1)
                {
                $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).' <b><small>('.$lead_perform->total_source.')</small></b></a></td>';
                }
                else
                {
                 $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).'</a></td>';   
                }
                $percent = ($lead_perform->repeatlLeads / ($lead_perform->repeatlLeads + $lead_perform->newLeads))*100;
                $percent1 = ($lead_perform->newLeads / ($lead_perform->repeatlLeads + $lead_perform->newLeads))*100;
                $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->repeatlLeads.' <span style="color:green">('.round($percent,2).'%)</span></a></td>';
                $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->newLeads.' <span style="color:green">('.round($percent1,2).'%)</span></a></td></tr>';

                $total_leads = $lead_perform->repeatlLeads + $lead_perform->newLeads + $total_leads;
                $total_repeat_leads = $total_repeat_leads + $lead_perform->repeatlLeads;
                $total__new_leads = $total__new_leads + $lead_perform->newLeads;
            }
            $html .= '<tr>';
            $html .= '<td>Total</td>';
            $html .= '<td>'.$total_leads.'</td>';
            $html .= '<td>'.$total_repeat_leads.'</td>';
            $html .= '<td>'.$total__new_leads.'</td>';
            $html .= '</tr>';
            $html .= '</tbody>';

            $details = [
                      'title' => 'Uploaded Leads',
                      'body' => $html,
                  ];
            // Mail::to('gaurav@classmonitor.com')->send(new \App\Mail\MyTestMail($details));
            Mail::to('vijeet@classmonitor.com')->send(new \App\Mail\MyTestMail($details));
            }
            else
            {
                $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
            }   
        // })->everyMinute();
        })->dailyAt('4:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
