<?php

namespace App\Http\Controllers\Backend;

use Hash;
use App\Models\Lead\Lead;
use App\Models\DailyTrack;
use Illuminate\Http\Request;
use App\Models\Access\User\User;
use App\Models\Lead\CallHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Lead\LeadRepository;
use App\Models\Data\DataUser;
use App\Events\Backend\Lead\LeadAssigned;
use Redirect;
use Auth;
use Mail;
/**
 * Class DashboardController.
 */

header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
class DashboardController extends Controller
{
    /**
     * @var LeadRepository
     */
    protected $leads;
    public $source_global = [];
    /**
     * @param LeadRepository $leads
     */
    public function __construct(LeadRepository $leads)
    {
        $this->leads = $leads;
    }

    /**
     * @return \Illuminate\View\View
     */
    // public function index(Request $request)
    // {
    //     $user = $request->user();

    //     $startDate = $request->get('startDate');
    //     $endDate = $request->get('endDate');
    //     $startDate = ($startDate != NULL & $startDate != '')? $startDate : date('Y-m-d');
    //     $endDate = ($endDate != NULL & $endDate != '')? $endDate : date('Y-m-d');

    //     $pickerStartDate = date('m/d/Y', strtotime($startDate));
    //     $pickerEndDate = date('m/d/Y', strtotime($endDate));

    //     if($user->hasRole('Administrator'))
    //     {
    //         return view('backend.dashboard.admin');
    //     }elseif($user->hasRole('Manager'))
    //     {
    //         // $tableData = array();
    //         // $executives = User::whereHas('roles', function($query){
    //         //                         $query->where('role_id', '3');
    //         //                         $query->orWhere('role_id', '2');
    //         //                     })
    //         //                     ->where('status', '1')
    //         //                     ->select('name', 'id')
    //         //                     ->get();
    //         // if($executives->count() > 0)
    //         // {
    //         //     foreach($executives as $executive)
    //         //     {
    //         //         $arr['name'] = $executive->name;
    //         //         $arr['id'] = $executive->id;
    //         //         // $arr['sale'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'sale')
    //         //         //                         ->count();
    //         //         // $arr['hot'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'hot')
    //         //         //                         ->count();
    //         //         // $arr['mild'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'mild')
    //         //         //                         ->count();
    //         //         // $arr['cold'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'cold')
    //         //         //                         ->count();
    //         //         // $arr['no_answer'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'no_answer')
    //         //         //                         ->count();
    //         //         // $arr['busy'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'busy')
    //         //         //                         ->count();
    //         //         // $arr['not_interested'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'not_interested')
    //         //         //                         ->count();
    //         //         // $arr['dead'] = CallHistory::where('called_by', $executive->id)
    //         //         //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
    //         //         //                         ->where('lead_status', 'dead')
    //         //         //                         ->count();

    //         //          $arr['sale'] = 1;
    //         //         $arr['hot'] = 1;
    //         //         $arr['mild'] = 1;
    //         //         $arr['cold'] = 1;
    //         //         $arr['no_answer'] = 1;
    //         //         $arr['busy'] = 1;
    //         //         $arr['not_interested'] = 1;
    //         //         $arr['dead'] = 1;
                    
    //         //         $tableData[] = $arr;
    //         //     }
    //         // }

    //         // $data = $this->leads->managerDashboardStats($startDate, $endDate);
    //      return view('backend.dashboard.manager', compact('startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
    //         // return view('backend.dashboard.manager', compact('data', 'startDate', 'endDate', 'pickerStartDate', 'pickerEndDate', 'tableData'));
    //     }elseif($user->hasRole('Executive'))
    //     {   
    //         // $data = $this->leads->executiveDashboardStats($user, $startDate, $endDate);
    //         return view('backend.dashboard.executive', compact('startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
    //     }
    // }

    public function index(Request $request)
    {
        $user = $request->user();

        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $startDate = ($startDate != NULL & $startDate != '')? $startDate : date('Y-m-d');
        $endDate = ($endDate != NULL & $endDate != '')? $endDate : date('Y-m-d');

        $pickerStartDate = date('m/d/Y', strtotime($startDate));
        $pickerEndDate = date('m/d/Y', strtotime($endDate));

        if($user->hasRole('Administrator'))
        {
            return view('backend.dashboard.admin');
        }elseif($user->hasRole('Manager'))
        {
            
            return view('backend.dashboard.manager', compact('startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
        }elseif($user->hasRole('Executive'))
        {   
            return view('backend.dashboard.executive', compact('startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
        }elseif($user->hasRole('Delight Team'))
        {   
            return view('backend.dashboard.delight', compact('startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
        }
    }

     public function getCalculation(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $startDate = ($startDate != NULL & $startDate != '')? $startDate : date('Y-m-d');
        $endDate = ($endDate != NULL & $endDate != '')? $endDate : date('Y-m-d');

        $pickerStartDate = date('m/d/Y', strtotime($startDate));
        $pickerEndDate = date('m/d/Y', strtotime($endDate));

        if($user->hasRole('Administrator'))
        {
            return view('backend.dashboard.admin');
        }elseif($user->hasRole('Manager'))
        {
            $tableData = array();
            $executives = User::whereHas('roles', function($query){
                                    $query->where('role_id', '3');
                                    $query->orWhere('role_id', '2');
                                })
                                ->where('status', '1')
                                ->select('name', 'id')
                                ->get();
            if($executives->count() > 0)
            {
                foreach($executives as $executive)
                {
                    // $arr['name'] = $executive->name;
                    // $arr['id'] = $executive->id;
                    // $data = \DB::table("call_history")
                    //   ->select('data_medium',DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'open' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as open"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'hot' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as hot"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'mild' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as mild"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'cold' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as cold"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'dead' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as dead"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'sale' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as sale"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'no_answer' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as no_answer"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'busy' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as busy"),
                    //            DB::raw("(SELECT count(id) FROM call_history
                    //                   WHERE call_history.called_by =".$executive->id." AND call_history.lead_status = 'not_interested' AND call_history.created_at >= '".$startDate."' AND call_history.created_at <= '".$endDate."'
                    //                 ) as not_interested")
                    //           )
                    // ->first();
                    // $arr['sale'] = $data->sale;
                    // $arr['hot'] = $data->hot;
                    // $arr['mild'] = $data->mild;
                    // $arr['cold'] = $data->cold;
                    // $arr['no_answer'] = $data->no_answer;
                    // $arr['busy'] = $data->busy;
                    // $arr['not_interested'] = $data->not_interested;
                    // $arr['dead'] = $data->dead;
                    // $arr['sale'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'sale')
                    //                         ->count();
                    // $arr['hot'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'hot')
                    //                         ->count();
                    // $arr['mild'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'mild')
                    //                         ->count();
                    // $arr['cold'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'cold')
                    //                         ->count();
                    // $arr['no_answer'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'no_answer')
                    //                         ->count();
                    // $arr['busy'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'busy')
                    //                         ->count();
                    // $arr['not_interested'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'not_interested')
                    //                         ->count();
                    // $arr['dead'] = CallHistory::where('called_by', $executive->id)
                    //                         ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                    //                         ->where('lead_status', 'dead')
                    //                         ->count();

                   
                    $arr['name'] = $executive->name;
                    $arr['id'] = $executive->id;
                    $data = DB::select("SELECT `lead_status`, COUNT(id) as total FROM `call_history` WHERE `called_by` = ".$executive->id." GROUP BY `lead_status`");
                    $arr['sale'] = 0;
                    $arr['hot'] = 0;
                    $arr['mild'] = 0;
                    $arr['cold'] = 0;
                    $arr['no_answer'] = 0;
                    $arr['busy'] = 0;
                    $arr['not_interested'] = 0;
                    $arr['dead'] = 0;
                    foreach($data as $vv)
                    {
                       $arr[$vv->lead_status] = $vv->total; 
                    }
                    $tableData[] = $arr;
                }
            }

            $data = $this->leads->managerDashboardStats($startDate, $endDate);
            return view('backend.dashboard.manager_refresh', compact('data', 'startDate', 'endDate', 'pickerStartDate', 'pickerEndDate', 'tableData'));
        }elseif($user->hasRole('Executive'))
        {   
            $data = $this->leads->executiveDashboardStats($user, $startDate, $endDate);
            return view('backend.dashboard.executive_refresh', compact('data', 'startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
        }
    }
    public function checkIn(Request $request)
    {
        $this->leads->userDailyTrack($request->user(), 'in');

        return redirect()->route('admin.lead.index')
                        ->withFlashSuccess(trans('alerts.backend.check_in'));
    }

    public function checkOut(Request $request)
    {
        $this->leads->userDailyTrack($request->user(), 'out');

        return redirect()->route('admin.lead.index')
                        ->withFlashSuccess(trans('alerts.backend.check_out'));
    }

    public function fetchLeads(Request $request)
    {
        $this->leads->assignLeads($request->user());

        return redirect()->route('admin.lead.index')
                        ->withFlashSuccess(trans('alerts.backend.leads_fetched'));
    }

     public function leadUpload(Request $request)
    {
        // $data = DB::table('upload_datas')
        //         ->where('date',date('Y-m-d'))
        //         ->select('*')
        //         ->get();
        
        
        $dataMediums = $this->leads->getMediumFilter();
        $data = DB::table('upload_datas')
                ->where('date',date('Y-m-d'))
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
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as total_source"),
                               DB::raw("(SELECT SUM(repeatlLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as repeatlLeads"),
                               DB::raw("(SELECT SUM(newLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as newLeads")
                              
                              )
                      ->first();
             // print_r($lead_perform);die();      
            $variable = "'".$value->source."','".date('Y-m-d')."','".date('Y-m-d')."'";   
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
        $html .= '<td></td>';
        $html .= '<td>'.$total_leads.'</td>';
        $html .= '<td>'.$total_repeat_leads.'</td>';
        $html .= '<td>'.$total__new_leads.'</td>';
        $html .= '</tr>';
        $html .= '</tbody>';

        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        
        return view('backend.dashboard.leadUpload', compact('html','dataMediums'));
    }

    public function leadUploadByDate(Request $request)
    {
        $data = DB::table('upload_datas')
                ->where('date','>=',$request->startDate)
                ->where('date','<=',$request->endDate)
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
            <tbody id="lead-performance">';
        if($data)
        {
        foreach($data as $value)
        {

            $lead_perform = \DB::table("upload_datas")
                      ->select('data_medium',
                                DB::raw("(SELECT count(id) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date = '".date('Y-m-d')."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as total_source"),
                               DB::raw("(SELECT SUM(repeatlLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date >= '".$request->startDate."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as repeatlLeads"),
                               DB::raw("(SELECT SUM(newLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value->source."' AND upload_datas.date >= '".$request->startDate."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as newLeads")
                              
                              )
                      ->first();
             // print_r($lead_perform);die();   
            $variable = "'".$value->source."','".$request->startDate."','".$request->endDate."'";        
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
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->repeatlLeads.'<span style="color:green"> ('.round($percent,2).'%)</span></a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->newLeads.' <span style="color:green">('.round($percent1,2).'%)</span></a></td></tr>';
            $total_leads = $lead_perform->repeatlLeads + $lead_perform->newLeads + $total_leads;
            $total_repeat_leads = $total_repeat_leads + $lead_perform->repeatlLeads;
            $total__new_leads = $total__new_leads + $lead_perform->newLeads;
        }
        $html .= '<tr>';
        $html .= '<td></td>';
        $html .= '<td>'.$total_leads.'</td>';
        $html .= '<td>'.$total_repeat_leads.'</td>';
        $html .= '<td>'.$total__new_leads.'</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</tbody>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        echo $html;
    }
    
     public function leadUploadDetails(Request $request)
    {
        $data = DB::table('upload_datas')
                ->where('date','>=',$request->startDate)
                ->where('date','<=',$request->endDate)
                ->where('data_medium',$request->medium)
                ->orderBy('created_at','desc')
                ->select('*','data_medium as source')
                ->get();
        $i = 0;
        $j = 0;
        $html = '<div class="table-responsive">
                <table id="upload-detail-data" class="table table-condensed  table-hover mt-5 header data-sticky-header"><thead>
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Total Leads</th>
                    <th>Repeat Leads</th>
                    <th>New Leads</th>
                </tr>
            </thead>
            <tbody style="height:500px !important; overflow-y:scroll">';
        if($data)
        {
        foreach($data as $value)
        {

             // print_r($lead_perform);die();  
            $new_date = date('d M, Y H:i:s', strtotime($value->created_at));       
            $html .= '<tr class="" id="source_id_'.$value->source.'">';
            $html .= '<td>'.$new_date.'</td>';
            $html .= '<td>'.$value->source.'</td>';
            $html .= '<td>'.($value->repeatlLeads + $value->newLeads).' </td>';
            $html .= '<td>'.$value->repeatlLeads.'</td>';
            $html .= '<td>'.$value->newLeads.'</td></tr>';
        }
        $html .= '</tbody></table>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        echo $html;
    }

    public function getuploadleadByfilter(Request $request)
    {
        $data = explode(',', $request->sources);
        // echo $request->startDate ;
        // echo $request->endDate ;
        // print_r($data);die("****");
        
        $html = '<thead>
                <tr>
                    <th>Source</th>
                    <th>Total Leads</th>
                    <th>Repeat Leads</th>
                    <th>New Leads</th>
                </tr>
            </thead>
            <tbody id="lead-performance" class="t-height">';
       
        foreach($data as $value)
        {
            
              if($request->startDate == 'undefined' && $request->endDate == 'undefined')
              {
                $lead_perform = \DB::table("upload_datas")
                      ->select('data_medium',
                                DB::raw("(SELECT count(id) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as total_source"),
                               DB::raw("(SELECT SUM(repeatlLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as repeatlLeads"),
                               DB::raw("(SELECT SUM(newLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date = '".date('Y-m-d')."' 
                                    ) as newLeads")
                              
                              )
                      ->first();
                // print_r($lead_perform);die("****");
              } 
              else
              { 
              $lead_perform = \DB::table("upload_datas")
                      ->select('data_medium',
                                DB::raw("(SELECT count(id) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date = '".$request->startDate."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as total_source"),
                               DB::raw("(SELECT SUM(repeatlLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date >= '".$request->startDate."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as repeatlLeads"),
                               DB::raw("(SELECT SUM(newLeads) FROM upload_datas
                                      WHERE upload_datas.data_medium = '".$value."' AND upload_datas.date >= '".$request->startDate."' AND upload_datas.date <= '".$request->endDate."'
                                    ) as newLeads")
                              
                              )
                      ->first();
                }
            $variable = "'".$value."','".$request->startDate."','".$request->endDate."'";        
            $html .= '<tr class="" id="source_id_'.$value.'">';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$value.'</a></td>';
            if($lead_perform->total_source > 1)
            {
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).' <b><small>('.$lead_perform->total_source.')</small></b></a></td>';
            }
            else
            {
             $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).'</a></td>';   
            }
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->repeatlLeads.'</a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->newLeads.'</a></td></tr>';
        }
        $html .= '</tbody>';
       
        echo $html;
    }

    // public function leadPerformance(Request $request)
    // {
    //     $data = Lead::whereNotNull('data_medium')
    //             ->where('data_medium','!=' ,'')
    //             ->groupBy('data_medium')
    //             ->select([
    //                 'data_medium', 
    //                 DB::raw('count(id) as totalLeads'),
    //                 DB::raw('(SELECT count(l.id) FROM leads as l WHERE l.data_medium=leads.data_medium AND l.lead_status="sale") as totalSales')
    //             ])
    //             ->get();
                
    //     return view('backend.dashboard.leadPerformance', compact('data'));
    // }


    public function leadPerformance(Request $request)
    {
        $dataMediums = $this->leads->getMediumFilter();
        $performances = DB::table('lead_performances')->select('*')->get();
        $html = '';
        $totalLeadCount = 0;
        $totalOpenCount = 0;
        $totalHotCount = 0;
        $totalMildCount = 0;
        $totalColdCount = 0;
        $totalDeadCount = 0;
        $totalSaleCount = 0;
        $totalNoanswerCount = 0;
        $totalBusyCount = 0;
        $totalNotinrestedCount = 0;
        if(count($performances) > 0)
        {
             $html .= '<thead>
                        <tr>
                            <th>Lead Source</th>
                            <th>New Leads</th>
                            <th>Open</th>
                            <th>Hot</th>
                            <th>Mild</th>
                            <th>Cold</th>
                            <th>Dead</th>
                            <th>Sale</th>
                            <th>No Answer</th>
                            <th>Busy</th>
                            <th>Not Intrested</th>
                        </tr>
                    </thead>
                    <tbody id="lead-performance" class="t-height">';
        foreach($performances as $value)
        {
             $totalLeadCount = $totalLeadCount + $value->totalLeads ;
            $totalOpenCount = $totalOpenCount + $value->open ;
            $totalHotCount = $totalHotCount + $value->hot;
            $totalMildCount = $totalMildCount + $value->mild;
            $totalColdCount = $totalColdCount + $value->cold;
            $totalDeadCount = $totalDeadCount + $value->dead;
            $totalSaleCount = $totalSaleCount + $value->sale;
            $totalNoanswerCount = $totalNoanswerCount + $value->no_answer;
            $totalBusyCount = $totalBusyCount + $value->busy;
            $totalNotinrestedCount = $totalNotinrestedCount + $value->not_interested;
            if($value->source == '')
            {
                $value->source = 'NO SOURCE';
            }
            if($value->totalLeads > 0)
            {
            $html .= '<tr><th>'.$value->source.'</th><td>'.$value->totalLeads.'</td>';
            $html .= '<td>'.$value->open.'<span class="text-success"> ('.round(($value->open/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->hot.'<span class="text-success"> ('.round(($value->hot/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->mild.'<span class="text-success"> ('.round(($value->mild/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->cold.'<span class="text-success"> ('.round(($value->cold/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->dead.'<span class="text-success"> ('.round(($value->dead/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->sale.'<span class="text-success"> ('.round(($value->sale/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->no_answer.'<span class="text-success"> ('.round(($value->no_answer/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->busy.'<span class="text-success"> ('.round(($value->busy/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->not_interested.'<span class="text-success"> ('.round(($value->not_interested/$value->totalLeads)*100,2).'%'.')</span> </td></tr>';
            }
        }
        $html .= '<tr style="border-top:1px solid"><td><strong>Total</strong></td>';
        $html .= '<td><strong>'.$totalLeadCount.'</strong></td>';
        $html .= '<td><strong>'.$totalOpenCount.' ('.round(($totalOpenCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalHotCount.' ('.round(($totalHotCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalMildCount.' ('.round(($totalMildCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalColdCount.' ('.round(($totalColdCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalDeadCount.' ('.round(($totalDeadCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalSaleCount.' ('.round(($totalSaleCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalNoanswerCount.' ('.round(($totalNoanswerCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalBusyCount.' ('.round(($totalBusyCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalNotinrestedCount.' ('.round(($totalNotinrestedCount/$totalLeadCount)*100,2).'%'.')</strong></td></tr></tbody>';
        return view('backend.dashboard.leadPerformance', compact('html','dataMediums'));
        }
        else
        {
           return view('backend.dashboard.leadPerformance', compact('html','dataMediums'));
        }
    }

    public function getleadPerformance(Request $request)
    {
        $leadPerformance_source = DB::table('lead_performances')->pluck('source');
        $data = DB::table('leads')
                // ->where('data_medium','!=' ,'')
                ->groupBy('data_medium')
                ->select('data_medium as source')
                ->get();
        $totalLeadCount = 0;
        $totalOpenCount = 0;
        $totalHotCount = 0;
        $totalMildCount = 0;
        $totalColdCount = 0;
        $totalDeadCount = 0;
        $totalSaleCount = 0;
        $totalNoanswerCount = 0;
        $totalBusyCount = 0;
        $totalNotinrestedCount = 0;
        $i = 0;
        $j = 0;
        foreach($data as $values)
        {

              $lead_perform = \DB::table("leads")
                      ->select('data_medium',
                                DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND (leads.lead_status = 'open' || leads.lead_status = '')
                                    ) as open"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'hot'
                                    ) as hot"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'mild'
                                    ) as mild"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'cold'
                                    ) as cold"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'dead'
                                    ) as dead"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'sale'
                                    ) as sale"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'no_answer'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'busy'
                                    ) as busy"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'not_interested'
                                    ) as not_interested")
                              )
                      ->first();

            $totalLeadCount = $totalLeadCount + $lead_perform->totalLeads ;
            $totalOpenCount = $totalOpenCount + $lead_perform->open ;
            $totalHotCount = $totalHotCount + $lead_perform->hot;
            $totalMildCount = $totalMildCount + $lead_perform->mild;
            $totalColdCount = $totalColdCount + $lead_perform->cold;
            $totalDeadCount = $totalDeadCount + $lead_perform->dead;
            $totalSaleCount = $totalSaleCount + $lead_perform->sale;
            $totalNoanswerCount = $totalNoanswerCount + $lead_perform->no_answer;
            $totalBusyCount = $totalBusyCount + $lead_perform->busy;
            $totalNotinrestedCount = $totalNotinrestedCount + $lead_perform->not_interested;

            if ($leadPerformance_source->contains($values->source)) 
            {
                $performance_data[$i] = array(
                    'source' => $values->source,
                    'totalLeads' => $lead_perform->totalLeads,
                    'open' => $lead_perform->open,
                    'hot' => $lead_perform->hot,
                    'mild' => $lead_perform->mild,
                    'cold' => $lead_perform->cold,
                    'dead' => $lead_perform->dead,
                    'sale' => $lead_perform->sale,
                    'no_answer' => $lead_perform->no_answer,
                    'busy' => $lead_perform->busy,
                    'not_interested' => $lead_perform->not_interested,
                    'created_at' => date('Y-m-d h:m:s'),
                    'updated_at' => date('Y-m-d h:m:s'),
                ); 
            }   
            else
            {
                $performance_data[$i] = array(
                        'source' => $values->source,
                        'totalLeads' => $lead_perform->totalLeads,
                        'open' => $lead_perform->open,
                        'hot' => $lead_perform->hot,
                        'mild' => $lead_perform->mild,
                        'cold' => $lead_perform->cold,
                        'dead' => $lead_perform->dead,
                        'sale' => $lead_perform->sale,
                        'no_answer' => $lead_perform->no_answer,
                        'busy' => $lead_perform->busy,
                        'not_interested' => $lead_perform->not_interested,
                        'created_at' => date('Y-m-d h:m:s'),
                        'updated_at' => date('Y-m-d h:m:s'),
                );
            }  
                $i++;
        }
  
        if (count($performance_data) > 0) 
            {
                $truncate = DB::table('lead_performances')->truncate();
                DB::table('lead_performances')->insert(
                 $performance_data
                );
            }   
            

        $performances = DB::table('lead_performances')->select('*')->get();
        $html = '<thead>
                        <tr>
                            <th>Lead Source</th>
                            <th>New Leads</th>
                            <th>Open</th>
                            <th>Hot</th>
                            <th>Mild</th>
                            <th>Cold</th>
                            <th>Dead</th>
                            <th>Sale</th>
                            <th>No Answer</th>
                            <th>Busy</th>
                            <th>Not Intrested</th>
                        </tr>
                    </thead>
                    <tbody id="lead-performance" class="t-height">';
        foreach($performances as $value)
        {
            if($value->source == '')
            {
                $value->source = 'NO SOURCE';
            }
            if($value->totalLeads > 0)
            {
            $html .= '<tr><th>'.$value->source.'</th><td>'.$value->totalLeads.'</td>';
            $html .= '<td>'.$value->open.'<span class="text-success"> ('.round(($value->open/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->hot.'<span class="text-success"> ('.round(($value->hot/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->mild.'<span class="text-success"> ('.round(($value->mild/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->cold.'<span class="text-success"> ('.round(($value->cold/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->dead.'<span class="text-success"> ('.round(($value->dead/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->sale.'<span class="text-success"> ('.round(($value->sale/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->no_answer.'<span class="text-success"> ('.round(($value->no_answer/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->busy.'<span class="text-success"> ('.round(($value->busy/$value->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$value->not_interested.'<span class="text-success"> ('.round(($value->not_interested/$value->totalLeads)*100,2).'%'.')</span> </td></tr>';
            }
        }
        if($totalLeadCount > 0)
        {
        $html .= '<tr style="border-top:1px solid"><td><strong>Total</strong></td>';
        $html .= '<td><strong>'.$totalLeadCount.'</strong></td>';
        $html .= '<td><strong>'.$totalOpenCount.' ('.round(($totalOpenCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalHotCount.' ('.round(($totalHotCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalMildCount.' ('.round(($totalMildCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalColdCount.' ('.round(($totalColdCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalDeadCount.' ('.round(($totalDeadCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalSaleCount.' ('.round(($totalSaleCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalNoanswerCount.' ('.round(($totalNoanswerCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalBusyCount.' ('.round(($totalBusyCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
        $html .= '<td><strong>'.$totalNotinrestedCount.' ('.round(($totalNotinrestedCount/$totalLeadCount)*100,2).'%'.')</strong></td></tr></tbody>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        echo $html;

    }

        public function getleadPerformanceByDate(Request $request)
    {
        
        if($request->sources != "")
        {
        $data = explode(',', $request->sources);
        }
        else
        {
            if($request->startDate == $request->endDate)
            {
                $data = DB::table('leads')
                    ->whereRaw("DATE(last_call)","'".$request->startDate."'")
                    ->groupBy('data_medium')
                    ->select('data_medium as source')
                    ->get();
            }
            else
            {
            $data = DB::table('leads')
                    ->where('last_call','>=',$request->startDate)
                    ->where('last_call','<=',$request->endDate)
                    ->groupBy('data_medium')
                    ->select('data_medium as source')
                    ->get();
            }
        }
       
        $totalLeadCount = 0;
        $totalOpenCount = 0;
        $totalHotCount = 0;
        $totalMildCount = 0;
        $totalColdCount = 0;
        $totalDeadCount = 0;
        $totalSaleCount = 0;
        $totalNoanswerCount = 0;
        $totalBusyCount = 0;
        $totalNotinrestedCount = 0;
        $html = '<thead>
                <tr>
                    <th>Lead Source</th>
                    <th>New Leads</th>
                    <th>Open</th>
                    <th>Hot</th>
                    <th>Mild</th>
                    <th>Cold</th>
                    <th>Dead</th>
                    <th>Sale</th>
                    <th>No Answer</th>
                    <th>Busy</th>
                    <th>Not Intrested</th>
                </tr>
            </thead>
            <tbody id="lead-performance" class="t-height">';
        foreach($data as $value)
        {   
            if($request->sources != "")
            {
                if($request->startDate == $request->endDate)
                {
                     $lead_perform = \DB::table("leads")
                          ->select('data_medium',DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.created_at) = '".$request->startDate."'
                                        ) as totalLeadsnew"),
                                    DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as totalLeads"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as open"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as hot"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as mild"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as cold"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as dead"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as sale"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as no_answer"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as busy"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as not_interested")
                                  )
                          ->first();
                }
                else
                {
                    
                     $lead_perform = \DB::table("leads")
                          ->select('data_medium',DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.created_at) >= '".$request->startDate."' AND DATE(leads.created_at) <= '".$request->endDate."'
                                        ) as totalLeadsnew"),
                                    DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as totalLeads"),

                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as open"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as hot"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as mild"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as cold"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as dead"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as sale"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as no_answer"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as busy"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as not_interested")
                                  )
                          ->first();
                }
            }  
            else
            {

                if($request->startDate == $request->endDate)
                {
                     $lead_perform = \DB::table("leads")
                          ->select('data_medium',DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND DATE(leads.created_at) = '".$request->startDate."'
                                        ) as totalLeadsnew"),
                                    DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as totalLeads"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as open"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as hot"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as mild"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as cold"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as dead"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as sale"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as no_answer"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as busy"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as not_interested")
                                  )
                          ->first();
                }
                else
                {
                     $lead_perform = \DB::table("leads")
                          ->select('data_medium',DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND DATE(leads.created_at) >= '".$request->startDate."' AND DATE(leads.created_at) <= '".$request->endDate."'
                                        ) as totalLeadsnew"),
                                    DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as totalLeads"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as open"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as hot"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as mild"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as cold"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as dead"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as sale"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as no_answer"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as busy"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                        ) as not_interested")
                                  )
                          ->first();
                }

            }
            if($lead_perform->totalLeads > 0)
            {
                $totalLeadCount = $totalLeadCount + $lead_perform->totalLeadsnew ;
                $totalOpenCount = $totalOpenCount + $lead_perform->open ;
                $totalHotCount = $totalHotCount + $lead_perform->hot;
                $totalMildCount = $totalMildCount + $lead_perform->mild;
                $totalColdCount = $totalColdCount + $lead_perform->cold;
                $totalDeadCount = $totalDeadCount + $lead_perform->dead;
                $totalSaleCount = $totalSaleCount + $lead_perform->sale;
                $totalNoanswerCount = $totalNoanswerCount + $lead_perform->no_answer;
                $totalBusyCount = $totalBusyCount + $lead_perform->busy;
                $totalNotinrestedCount = $totalNotinrestedCount + $lead_perform->not_interested;
                if($request->sources != "")
                {
                    $html .= '<tr class="common_source_class" id="source_id_'.$value.'"><th>'.$value.'</th>';
                }
                else
                {
                    $html .= '<tr class="common_source_class" id="source_id_'.$value->source.'"><th>'.$value->source.'</th>';
                }
                
                $html .= '<td>'.$lead_perform->totalLeadsnew.'</td>';
                $html .= '<td>'.$lead_perform->open.'<span class="text-success"> ('.round(($lead_perform->open/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->hot.'<span class="text-success"> ('.round(($lead_perform->hot/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->mild.'<span class="text-success"> ('.round(($lead_perform->mild/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->cold.'<span class="text-success"> ('.round(($lead_perform->cold/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->dead.'<span class="text-success"> ('.round(($lead_perform->dead/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->sale.'<span class="text-success"> ('.round(($lead_perform->sale/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->no_answer.'<span class="text-success"> ('.round(($lead_perform->no_answer/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->busy.'<span class="text-success"> ('.round(($lead_perform->busy/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
                $html .= '<td>'.$lead_perform->not_interested.'<span class="text-success"> ('.round(($lead_perform->not_interested/$lead_perform->totalLeads)*100,2).'%'.')</span> </td></tr>';
                
            }
        }
        if($totalLeadCount > 0)
        {
            $html .= '<tr style="border-top:1px solid"><td><strong>Total</strong></td>';
            $html .= '<td><strong>'.$totalLeadCount.'</strong></td>';
            $html .= '<td><strong>'.$totalOpenCount.' ('.round(($totalOpenCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalHotCount.' ('.round(($totalHotCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalMildCount.' ('.round(($totalMildCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalColdCount.' ('.round(($totalColdCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalDeadCount.' ('.round(($totalDeadCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalSaleCount.' ('.round(($totalSaleCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalNoanswerCount.' ('.round(($totalNoanswerCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalBusyCount.' ('.round(($totalBusyCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalNotinrestedCount.' ('.round(($totalNotinrestedCount/$totalLeadCount)*100,2).'%'.')</strong></td></tr></tbody>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
            $totalLeadCount = 0;
        $totalOpenCount = 0;
        $totalHotCount = 0;
        $totalMildCount = 0;
        $totalColdCount = 0;
        $totalDeadCount = 0;
        $totalSaleCount = 0;
        $totalNoanswerCount = 0;
        $totalBusyCount = 0;
        $totalNotinrestedCount = 0;
        }
        echo $html;
    }
    
    public function getleadPerformanceByfilter(Request $request)
    {
        $data = explode(',', $request->sources);
        $totalLeadCount = 0;
        $totalOpenCount = 0;
        $totalHotCount = 0;
        $totalMildCount = 0;
        $totalColdCount = 0;
        $totalDeadCount = 0;
        $totalSaleCount = 0;
        $totalNoanswerCount = 0;
        $totalBusyCount = 0;
        $totalNotinrestedCount = 0;
        $html = '<thead>
                <tr>
                    <th>Lead Source</th>
                    <th>New Leads</th>
                    <th>Open</th>
                    <th>Hot</th>
                    <th>Mild</th>
                    <th>Cold</th>
                    <th>Dead</th>
                    <th>Sale</th>
                    <th>No Answer</th>
                    <th>Busy</th>
                    <th>Not Intrested</th>
                </tr>
            </thead>
            <tbody id="lead-performance" class="t-height">';
        foreach($data as $value)
        {
              if(($request->startDate == 'undefined' || $request->startDate == '') && ($request->endDate == 'undefined' || $request->endDate == ''))
              {
                $lead_perform = \DB::table("leads")
                      ->select('data_medium',
                                DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '')
                                    ) as open"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot'
                                    ) as hot"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild'
                                    ) as mild"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold'
                                    ) as cold"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead'
                                    ) as dead"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale'
                                    ) as sale"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy'
                                    ) as busy"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested'
                                    ) as not_interested")
                              )
                      ->first();
              } 
              else
              { 

               if($request->startDate == $request->endDate)
                {
                     $lead_perform = \DB::table("leads")
                          ->select('data_medium',DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.created_at) = '".$request->startDate."'
                                        ) as totalLeadsnew"),
                                    DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as totalLeads"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as open"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as hot"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as mild"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as cold"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as dead"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) = '".$request->startDate."' 
                                        ) as sale"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as no_answer"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as busy"),
                                   DB::raw("(SELECT count(id) FROM leads
                                          WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) = '".$request->startDate."'
                                        ) as not_interested")
                                  )
                          ->first();
                }
                else
                { 
              $lead_perform = \DB::table("leads")
                      ->select('data_medium',
                        DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.created_at >= '".$request->startDate."' AND DATE(leads.created_at) <= '".$request->endDate."'
                                    ) as totalLeadsnew"),
                        DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.last_call >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as open"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as hot"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as mild"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as cold"),
                               DB::raw("(SELECT coun(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as dead"),
                               DB::raw("(SELECT coun(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as sale"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as busy"),
                               DB::raw("(SELECT count(id) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested' AND DATE(leads.last_call) >= '".$request->startDate."' AND DATE(leads.last_call) <= '".$request->endDate."'
                                    ) as not_interested")
                              )
                      ->first();
                  }
                }
            if($lead_perform->totalLeads > 0)
            {
            $totalLeadCount = $totalLeadCount + $lead_perform->totalLeadsnew ;
            $totalOpenCount = $totalOpenCount + $lead_perform->open ;
            $totalHotCount = $totalHotCount + $lead_perform->hot;
            $totalMildCount = $totalMildCount + $lead_perform->mild;
            $totalColdCount = $totalColdCount + $lead_perform->cold;
            $totalDeadCount = $totalDeadCount + $lead_perform->dead;
            $totalSaleCount = $totalSaleCount + $lead_perform->sale;
            $totalNoanswerCount = $totalNoanswerCount + $lead_perform->no_answer;
            $totalBusyCount = $totalBusyCount + $lead_perform->busy;
            $totalNotinrestedCount = $totalNotinrestedCount + $lead_perform->not_interested;
            $html .= '<tr class="common_source_class" id="source_id_'.$value.'"><th>'.$value.'</th>';
            $html .= '<td>'.$lead_perform->totalLeadsnew.'</td>';
            $html .= '<td>'.$lead_perform->open.'<span class="text-success"> ('.round(($lead_perform->open/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->hot.'<span class="text-success"> ('.round(($lead_perform->hot/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->mild.'<span class="text-success"> ('.round(($lead_perform->mild/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->cold.'<span class="text-success"> ('.round(($lead_perform->cold/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->dead.'<span class="text-success"> ('.round(($lead_perform->dead/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->sale.'<span class="text-success"> ('.round(($lead_perform->sale/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->no_answer.'<span class="text-success"> ('.round(($lead_perform->no_answer/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->busy.'<span class="text-success"> ('.round(($lead_perform->busy/$lead_perform->totalLeads)*100,2).'%'.')</span> </td>';
            $html .= '<td>'.$lead_perform->not_interested.'<span class="text-success"> ('.round(($lead_perform->not_interested/$lead_perform->totalLeads)*100,2).'%'.')</span> </td></tr>';
            }
        }
        if($totalLeadCount > 0)
        {
            $html .= '<tr style="border-top:1px solid"><td><strong>Total</strong></td>';
            $html .= '<td><strong>'.$totalLeadCount.'</strong></td>';
            $html .= '<td><strong>'.$totalOpenCount.' ('.round(($totalOpenCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalHotCount.' ('.round(($totalHotCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalMildCount.' ('.round(($totalMildCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalColdCount.' ('.round(($totalColdCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalDeadCount.' ('.round(($totalDeadCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalSaleCount.' ('.round(($totalSaleCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalNoanswerCount.' ('.round(($totalNoanswerCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalBusyCount.' ('.round(($totalBusyCount/$totalLeadCount)*100,2).'%'.')</strong></td>';
            $html .= '<td><strong>'.$totalNotinrestedCount.' ('.round(($totalNotinrestedCount/$totalLeadCount)*100,2).'%'.')</strong></td></tr></tbody>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        echo $html;
    }


    public function otp(Request $request)
    {
        $search_term = $request->get('q');
        if($search_term) {
            $data =  DB::connection('login')
                        ->table(config('table.login.users'))
                        ->where('phone', 'like', $search_term.'%')
                        ->orderBy('updated_at', 'DESC')
                        ->limit(100)
                        ->get();
        }else{
            $data =  DB::connection('login')
                        ->table(config('table.login.users'))
                        ->whereNotNull('otp')
                        ->orderBy('updated_at', 'DESC')
                        ->limit(100)
                        ->get();
        }

        return view('backend.dashboard.otp', compact('data', 'search_term'));
    }

     public function hmvisits(Request $request)
    {
        
        $data = DB::table('contact_data')
                    ->select('*')
                    ->orderBy('id', 'desc')
                    ->paginate(20);
        return view('backend.dashboard.hmvisits', compact('data'));
    }

    function resetPassword(Request $request)
    {
        DB::connection('login')
                        ->table(config('table.login.users'))
                        ->where('id', $request->get('rowID'))
                        ->update([
                            'password' => Hash::make('123456')
                        ]);
        return response()->json(['status' => 'success']);
    }
       public function calltest(Request $request)
    {
       
        $dataMediums = $this->leads->getMediumFilter();
        $html = '';
        
           return view('backend.dashboard.callTest', compact('html','dataMediums'));
    }
    public function getCallResponse()
    {
        $apiKey = 'a138e183-15b5-45c8-a05b-c2ab3f173be5';
        $loginid = 'riseom1';
        $callerid = '02235155017';
        $phonenumber = '9685049688'; 
        $format = 'json';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL,"https://app.office24by7.com/v1/communication/API/clickToCall");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, " apiKey=$apiKey&agentloginid=$agentloginid&servienumber=$servienumber&customernumber=$customernumber&format=$format");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $get_api_response = curl_exec($ch);
        // // curl_close($ch);
        // echo $get_api_response = curl_exec($ch); 

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL,"https://app.office24by7.com/v1/communication/API/clickToDial");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_POSTFIELDS,"apiKey=a138e183-15b5-45c8-a05b-c2ab3f173be5&loginid=riseom1&callerid=8349909168&phonenumber=9685049688&format=json");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // echo $get_api_response = curl_exec($ch);
        // curl_close($ch);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://app.office24by7.com/v1/communication/API/clickToDial");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, " apiKey=$apiKey&loginid=$loginid&callerid=$callerid&phonenumber=$phonenumber&format=$format");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        echo $get_api_response = curl_exec($ch);
        curl_close($ch);

  
    }
   public function salesReport(Request $request)
    {
        $array = array('January','February','March','April','May','June','July','August','September','October','November','December');

        //Daily Sales data
        $date = date("Y-m-d", strtotime("-30 day"));

        $sales_daily = DB::select("SELECT DATE_FORMAT(DATE(created_at),'%d %b') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND  DATE(created_at) >= '".$date."' GROUP BY DATE_FORMAT(DATE(created_at),'%d %b') ORDER BY DATE(created_at) DESC");

        $sales_daily = json_encode($sales_daily);

        //Monthly Sales data
        $year = date("Y");

        $sales_monthly = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE DATE_FORMAT(valid_from,'%Y') = '2021' AND `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");


        $sales_data = array();
        $k=0;
        $l=0;
        $months = array();
        foreach($sales_monthly as $aa)
        {
            $months[$l]=$aa->label;
            $l++;
        }
        for($i=0; $i < count($array);$i++)
        {
            if(in_array($array[$i],$months))
            {
                 $sales_data[$i]['label'] = $sales_monthly[$k]->label;   
                 $sales_data[$i]['y'] = $sales_monthly[$k]->y;  
                 $k++;
                
            }
            else
            {
                $sales_data[$i]['label'] = $array[$i];   
                $sales_data[$i]['y'] = 0; 
            }
          
        }
        

        // print_r($data);die("*****");
        $sales_monthly = json_encode($sales_data);

        //Previous year Monthly Sales data
        $year1 = date("Y",strtotime("-1 year"));

        $sales_monthly1 = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND DATE_FORMAT(valid_from,'%Y') = '".$year1."' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");


        $sales_data1 = array();
        $k1=0;
        $l1=0;
        $months1 = array();
        foreach($sales_monthly1 as $aa1)
        {
            $months1[$l1]=$aa1->label;
            $l1++;
        }
        for($ii=0; $ii < count($array);$ii++)
        {
            if(in_array($array[$ii],$months1))
            {
                 $sales_data1[$ii]['label'] = $sales_monthly1[$k1]->label;   
                 $sales_data1[$ii]['y'] = $sales_monthly1[$k1]->y;  
                 $k1++;
                
            }
            else
            {
                $sales_data1[$ii]['label'] = $array[$ii];   
                $sales_data1[$ii]['y'] = 0; 
            }
          
        }
        $sales_monthly1 = json_encode($sales_data1);

         //Previous 2 year Monthly Sales data
        $year2 = date("Y",strtotime("-2 year"));

        $sales_monthly2 = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND DATE_FORMAT(valid_from,'%Y') = '".$year2."' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");

        $sales_data2 = array();
        $k2=0;
        $l2=0;
        $months1 = array();
        foreach($sales_monthly2 as $aa1)
        {
            $months1[$l2]=$aa1->label;
            $l2++;
        }
        for($i2=0; $i2 < count($array);$i2++)
        {
            if(in_array($array[$i2],$months1))
            {
                 $sales_data2[$i2]['label'] = $sales_monthly2[$k2]->label;   
                 $sales_data2[$i2]['y'] = $sales_monthly2[$k2]->y;  
                 $k2++;
                
            }
            else
            {
                $sales_data2[$i2]['label'] = $array[$i2];   
                $sales_data2[$i2]['y'] = 0; 
            }
          
        }
        $sales_monthly2 = json_encode($sales_data2);

        //Yearly Sales Data
        $yearly_sales = DB::select("SELECT DATE_FORMAT(valid_from,'%Y') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%Y') ORDER BY DATE_FORMAT(valid_from,'%Y')");

        $yearly_sales = json_encode($yearly_sales);


        //Daily Sales Data By Source
        $startDate = date('Y-m-d',strtotime('first day of this month'));
        $endDate = date('Y-m-d');
        // $endDate = date('Y-m-d',strtotime('last day of this month'));


        $first_dateLastMonth = date('Y-m-d',strtotime('first day of last month'));
        $last_dateLastMonth = date('Y-m-d',strtotime('last day of last month'));

        $data_source = DB::table('subscriptions');
        $data_source = $data_source->whereDate('created_at', '>=', $startDate);
        $data_source = $data_source->whereDate('created_at', '<=', $endDate);
        $data_source = $data_source->where('subscription_type', 'PAID');
        $data_source = $data_source->groupBy('data_medium');
        $data_source = $data_source->select('data_medium', DB::raw('COUNT(id) as total'));
        $data_source = $data_source->orderBy('total','desc');
        $data_source = $data_source->get();

        // $today_sales = DB::table('leads')->whereDate('last_call', '=', date('Y-m-d'))->where('lead_status', 'sale')->select('id')->count();
        $today_sales = DB::table('subscriptions')->whereDate('created_at', '=', date('Y-m-d'))->where('subscription_type', 'PAID')->select('id')->count();
        $yesterday_sales = DB::table('subscriptions')->whereDate('created_at', '=', date("Y-m-d", strtotime("-1 day")))->where('subscription_type', 'PAID')->select('id')->count();

        $currentMonth_sales = DB::table('subscriptions')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->where('subscription_type', 'PAID')->select('id')->count();
        $lastMonth_sales = DB::table('subscriptions')->whereDate('created_at', '>=', $first_dateLastMonth)->whereDate('created_at', '<=', $last_dateLastMonth)->select('id')->count();
        return view('backend.dashboard.sales_reports', compact('sales_daily','sales_monthly','sales_monthly1','sales_monthly2','yearly_sales','data_source','startDate','endDate','today_sales','yesterday_sales','currentMonth_sales','lastMonth_sales'));
    }

       public function getSalesByDate(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $data_source = DB::table('subscriptions');
        $data_source = $data_source->whereDate('created_at', '>=', $startDate);
        $data_source = $data_source->whereDate('created_at', '<=', $endDate);
        $data_source = $data_source->where('subscription_type', 'PAID');
        $data_source = $data_source->groupBy('data_medium');
        $data_source = $data_source->select('data_medium', DB::raw('COUNT(id) as total'));
        $data_source = $data_source->orderBy('total','desc');
        $data_source = $data_source->get();

        $sourceConversionTable = array();
        $i = 1;
        $count = 0;
        $html = '';
        foreach($data_source as $source)
        {
           
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$source->data_medium.'</td>';
                $html .= '<td>'.$source->total.'</td>';
                $html .= '</tr>';

                $i++;
                $count = $source->total + $count;
        }
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td><strong>Total</strong></td>';
                $html .= '<td><strong>'.$count.'</strong></td>';
                $html .= '</tr>';
        echo $html;
    }

    // public function salesReport(Request $request)
    // {
    //     $array = array('January','February','March','April','May','June','July','August','September','October','November','December');

    //     //Daily Sales data
    //     $date = date("Y-m-d", strtotime("-30 day"));
    //     // $sales_daily = DB::select("SELECT DATE_FORMAT(DATE(last_call),'%d %b') as label,COUNT(id) as y FROM `leads` WHERE `lead_status` = 'sale' AND DATE(last_call) >= '".$date."' GROUP BY DATE_FORMAT(DATE(last_call),'%d %b') ORDER BY DATE(last_call) DESC");

    //     $sales_daily = DB::select("SELECT DATE_FORMAT(valid_from,'%d %b') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND  valid_from >= '".$date."' GROUP BY DATE_FORMAT(valid_from,'%d %b') ORDER BY valid_from DESC");

    //     // $sales_daily = DB::select("SELECT DATE_FORMAT(DATE(created_at),'%d %b') as label,COUNT(id) as y FROM `tbl_learning_subscriptions` WHERE `created_at` >= '".$date."' `valid_from` = '2021-12-07' AND `subscription_type` = 'PAID' ORDER BY `id` DESC");
    //     $sales_daily = json_encode($sales_daily);

    //     //Monthly Sales data
    //     $year = date("Y");
    //     // $sales_monthly = DB::select("SELECT DATE_FORMAT(DATE(last_call),'%M') as label,COUNT(id) as y FROM `leads` WHERE `lead_status` = 'sale' AND DATE_FORMAT(DATE(last_call),'%Y') = '".$year."' GROUP BY DATE_FORMAT(DATE(last_call),'%M') ORDER BY DATE(last_call)");

    //     // $sales_monthly = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID'  AND DATE_FORMAT(valid_from,'%Y') = '".$year."' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");

    //     $sales_monthly = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE DATE_FORMAT(valid_from,'%Y') = '2021' AND `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");


    //     $sales_data = array();
    //     $k=0;
    //     $l=0;
    //     $months = array();
    //     foreach($sales_monthly as $aa)
    //     {
    //         $months[$l]=$aa->label;
    //         $l++;
    //     }
    //     for($i=0; $i < count($array);$i++)
    //     {
    //         if(in_array($array[$i],$months))
    //         {
    //              $sales_data[$i]['label'] = $sales_monthly[$k]->label;   
    //              $sales_data[$i]['y'] = $sales_monthly[$k]->y;  
    //              $k++;
                
    //         }
    //         else
    //         {
    //             $sales_data[$i]['label'] = $array[$i];   
    //             $sales_data[$i]['y'] = 0; 
    //         }
          
    //     }
        

    //     // print_r($data);die("*****");
    //     $sales_monthly = json_encode($sales_data);

    //     //Previous year Monthly Sales data
    //     $year1 = date("Y",strtotime("-1 year"));
    //     // $sales_monthly1 = DB::select("SELECT DATE_FORMAT(DATE(last_call),'%M') as label,COUNT(id) as y FROM `leads` WHERE `lead_status` = 'sale' AND DATE_FORMAT(DATE(last_call),'%Y') = '".$year1."' GROUP BY DATE_FORMAT(DATE(last_call),'%M') ORDER BY DATE(last_call)");

    //     $sales_monthly1 = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND DATE_FORMAT(valid_from,'%Y') = '".$year1."' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");


    //     $sales_data1 = array();
    //     $k1=0;
    //     $l1=0;
    //     $months1 = array();
    //     foreach($sales_monthly1 as $aa1)
    //     {
    //         $months1[$l1]=$aa1->label;
    //         $l1++;
    //     }
    //     for($ii=0; $ii < count($array);$ii++)
    //     {
    //         if(in_array($array[$ii],$months1))
    //         {
    //              $sales_data1[$ii]['label'] = $sales_monthly1[$k1]->label;   
    //              $sales_data1[$ii]['y'] = $sales_monthly1[$k1]->y;  
    //              $k1++;
                
    //         }
    //         else
    //         {
    //             $sales_data1[$ii]['label'] = $array[$ii];   
    //             $sales_data1[$ii]['y'] = 0; 
    //         }
          
    //     }
    //     $sales_monthly1 = json_encode($sales_data1);

    //      //Previous 2 year Monthly Sales data
    //     $year2 = date("Y",strtotime("-2 year"));
    //     // $sales_monthly2 = DB::select("SELECT DATE_FORMAT(DATE(last_call),'%M') as label,COUNT(id) as y FROM `leads` WHERE `lead_status` = 'sale' AND DATE_FORMAT(DATE(last_call),'%Y') = '".$year1."' GROUP BY DATE_FORMAT(DATE(last_call),'%M') ORDER BY DATE(last_call)");

    //     $sales_monthly2 = DB::select("SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' AND DATE_FORMAT(valid_from,'%Y') = '".$year2."' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from");

    //     $sales_data2 = array();
    //     $k2=0;
    //     $l2=0;
    //     $months1 = array();
    //     foreach($sales_monthly2 as $aa1)
    //     {
    //         $months1[$l2]=$aa1->label;
    //         $l2++;
    //     }
    //     for($i2=0; $i2 < count($array);$i2++)
    //     {
    //         if(in_array($array[$i2],$months1))
    //         {
    //              $sales_data2[$i2]['label'] = $sales_monthly2[$k2]->label;   
    //              $sales_data2[$i2]['y'] = $sales_monthly2[$k2]->y;  
    //              $k2++;
                
    //         }
    //         else
    //         {
    //             $sales_data2[$i2]['label'] = $array[$i2];   
    //             $sales_data2[$i2]['y'] = 0; 
    //         }
          
    //     }
    //     $sales_monthly2 = json_encode($sales_data2);

    //     //Yearly Sales Data
    //     // $yearly_sales = DB::select("SELECT DATE_FORMAT(DATE(last_call),'%Y') as label,COUNT(id) as y FROM `leads` WHERE `lead_status` = 'sale' GROUP BY DATE_FORMAT(DATE(last_call),'%Y') ORDER BY DATE_FORMAT(DATE(last_call),'%Y')");

    //     // $yearly_sales = DB::select("SELECT DATE_FORMAT(valid_from,'%Y') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%Y') ORDER BY DATE_FORMAT(valid_from,'%Y')");

    //     $yearly_sales = DB::select("SELECT DATE_FORMAT(valid_from,'%Y') as label,COUNT(id) as y FROM `subscriptions` WHERE `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%Y') ORDER BY DATE_FORMAT(valid_from,'%Y')");

    //     // SELECT DATE_FORMAT(valid_from,'%M') as label,COUNT(id) as y FROM `tbl_learning_subscriptions` WHERE DATE_FORMAT(valid_from,'%Y') = '2021' AND `subscription_type` = 'PAID' GROUP BY DATE_FORMAT(valid_from,'%M') ORDER BY valid_from

    //     // print_r($yearly_sales);die("*****");
    //     $yearly_sales = json_encode($yearly_sales);


    //     //Daily Sales Data By Source
    //     // $startDate = date('Y-m-01');;
    //     // $endDate = date('Y-m-t');

    //     $startDate = date('Y-m-d',strtotime('first day of this month'));
    //     $endDate = date('Y-m-d',strtotime('last day of this month'));


    //     $first_dateLastMonth = date('Y-m-d',strtotime('first day of last month'));
    //     $last_dateLastMonth = date('Y-m-d',strtotime('last day of last month'));

    //     // $data_source = DB::table('leads');
    //     // $data_source = $data_source->whereDate('last_call', '>=', $startDate);
    //     // $data_source = $data_source->whereDate('last_call', '<=', $endDate);
    //     // $data_source = $data_source->where('lead_status', 'sale');
    //     // $data_source = $data_source->groupBy('data_medium');
    //     // $data_source = $data_source->select('data_medium', DB::raw('COUNT(id) as total'));
    //     // $data_source = $data_source->orderBy('total','desc');
    //     // $data_source = $data_source->get();

    //     $data_source = DB::table('subscriptions');
    //     $data_source = $data_source->whereDate('valid_from', '>=', $startDate);
    //     $data_source = $data_source->whereDate('valid_from', '<=', $endDate);
    //     // $data_source = $data_source->whereDate('valid_from', '>=', '2021-11-01');
    //     // $data_source = $data_source->whereDate('valid_from', '<=', '2021-11-30');
    //     $data_source = $data_source->where('subscription_type', 'PAID');
    //     $data_source = $data_source->groupBy('data_medium');
    //     $data_source = $data_source->select('data_medium', DB::raw('COUNT(id) as total'));
    //     $data_source = $data_source->orderBy('total','desc');
    //     $data_source = $data_source->get();

    //     // $today_sales = DB::table('leads')->whereDate('last_call', '=', date('Y-m-d'))->where('lead_status', 'sale')->select('id')->count();
    //     $today_sales = DB::table('subscriptions')->whereDate('valid_from', '=', date('Y-m-d'))->where('subscription_type', 'PAID')->select('id')->count();
    //     // $yesterday_sales = DB::table('leads')->whereDate('last_call', '=', date("Y-m-d", strtotime("-1 day")))->where('lead_status', 'sale')->select('id')->count();
    //     $yesterday_sales = DB::table('subscriptions')->whereDate('valid_from', '=', date("Y-m-d", strtotime("-1 day")))->where('subscription_type', 'PAID')->select('id')->count();

    //     // $currentMonth_sales = DB::table('leads')->whereDate('last_call', '>=', $startDate)->whereDate('last_call', '<=', $endDate)->where('lead_status', 'sale')->select('id')->count();
    //     $currentMonth_sales = DB::table('subscriptions')->whereDate('valid_from', '>=', $startDate)->whereDate('valid_from', '<=', $endDate)->where('subscription_type', 'PAID')->select('id')->count();
    //     // $lastMonth_sales = DB::table('leads')->whereDate('last_call', '>=', $first_dateLastMonth)->whereDate('last_call', '<=', $last_dateLastMonth)->where('lead_status', 'sale')->select('id')->count();
    //     $lastMonth_sales = DB::table('subscriptions')->whereDate('created_at', '>=', $first_dateLastMonth)->whereDate('created_at', '<=', $last_dateLastMonth)->select('id')->count();
    //     // echo $today_sales; die("****");
    //     // $today_sales = DB::select("SELECT COUNT(id) as today_sales FROM `leads` WHERE `lead_status` = 'sale' GROUP BY DATE_FORMAT(DATE(last_call),'%Y') ORDER BY DATE_FORMAT(DATE(last_call),'%Y')");

    //     return view('backend.dashboard.sales_reports', compact('sales_daily','sales_monthly','sales_monthly1','sales_monthly2','yearly_sales','data_source','startDate','endDate','today_sales','yesterday_sales','currentMonth_sales','lastMonth_sales'));
    // }
    public function leadsReport(Request $request)
    {
        $array = array('January','February','March','April','May','June','July','August','September','October','November','December');

        $startDate = date('Y-m-01');
        // $startDate = "'".date('Y-m-01')."'";
        $endDate = date('Y-m-d');
        // $endDate = "'".date('Y-m-d')."'";

        $first_dateLastMonth = date('Y-m-d',strtotime('first day of last month'));
        $last_dateLastMonth = date('Y-m-d',strtotime('last day of last month'));

        $today_leads = DB::table('data_user')->whereDate('created_at', '=', date('Y-m-d'))->select('id')->count();
        $yesterday_leads = DB::table('data_user')->whereDate('created_at', '=', date("Y-m-d", strtotime("-1 day")))->select('id')->count();
        
        $currentMonth_leads = DB::table('data_user')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->select('id')->count();

        $lastMonth_leads = DB::table('data_user')->whereDate('created_at', '>=', $first_dateLastMonth)->whereDate('created_at', '<=', $last_dateLastMonth)->select('id')->count();

        $leads_daily = DB::select("SELECT data_medium  as label,COUNT(id) as y FROM data_user WHERE DATE(created_at) = '".date('Y-m-d')."' GROUP BY data_medium ORDER BY y DESC");
        $leads_daily = json_encode($leads_daily);


        $date1 = date("Y-m-d", strtotime("-29 day"));
        $date2 = date('Y-m-d');
        $subs_source = DB::select("SELECT data_medium  as source,COUNT(id) as y FROM subscriptions WHERE DATE(created_at) >= '".$date1."' AND DATE(created_at) <= '".$date2."' GROUP BY data_medium ORDER BY y DESC");
        $sourceConversionRate = array();
        $sourceConversionTable = array();
        $c = 0;
        $jj = 0;

        foreach($subs_source as $sources)
        {
            $totalSubs = $sources->y;
            $getDattUserTotal = DB::select("SELECT COUNT(*) as total FROM data_user WHERE data_medium = '".$sources->source."' AND date(`created_at`) >= '".$date1."' AND date(`created_at`) <= '".date('Y-m-d')."'");
            // print_r($getDattUserTotal);die();
            if($getDattUserTotal[0]->total == 0)
            {
                $getDattUserTotal[0]->total = 1;
            }
            $percentage = ($totalSubs/$getDattUserTotal[0]->total) * 100;
            if(round($percentage,2) <= 100)
            {
                $sourceConversionRate[$jj]['label'] = $sources->source;   
                $sourceConversionRate[$jj]['y'] = round($percentage,2);  
                $jj++;
            }
            //Table Data
            $sourceConversionTable[$c]['source_data'] = $sources->source; 
            $sourceConversionTable[$c]['totalSubs'] = $totalSubs; 
            $sourceConversionTable[$c]['Totalleads'] = $getDattUserTotal[0]->total; 
            $sourceConversionTable[$c]['percentage'] = round($percentage,2); 
            $c++;
        }

        $leads_monthlyPerformance = json_encode($sourceConversionRate);
        
        return view('backend.dashboard.leads_reports', compact('leads_daily','leads_monthlyPerformance','today_leads','yesterday_leads','currentMonth_leads','lastMonth_leads','sourceConversionTable'));
    }

    public function getleadConversionByDate(Request $request)
    {
        $date1 = $request->startDate;
        $date2 = $request->endDate;

        $subs_source = DB::select("SELECT data_medium  as source,COUNT(id) as y FROM subscriptions WHERE DATE(created_at) >= '".$date1."' AND DATE(created_at) <= '".$date2."' GROUP BY data_medium ORDER BY y DESC");
        $sourceConversionRate = array();
        $sourceConversionTable = array();
        $ll = 1;
        $html = '';
        foreach($subs_source as $sources)
        {
            $totalSubs = $sources->y;
            $getDattUserTotal = DB::select("SELECT COUNT(id) as total FROM data_user WHERE data_medium = '".$sources->source."' AND date(`created_at`) >= '".$date1."' AND date(`created_at`) <= '".$date2."'");
            if($getDattUserTotal[0]->total == 0)
            {
                $getDattUserTotal[0]->total = 1;
            }
            $percentage = ($totalSubs/$getDattUserTotal[0]->total) * 100;
            if($request->status == 'Active')
            {
                if(round($percentage,2) <= 100)
                    {
                        $html .= '<tr>';
                        $html .= '<td>'.$ll.'</td>';
                        $html .= '<td>'.$sources->source.'</td>';
                        $html .= '<td>'.$totalSubs.'</td>';
                        $html .= '<td>'.$getDattUserTotal[0]->total.'</td>';
                        $html .= '<td>'.round($percentage,2).'%</td>';
                        $html .= '</tr>';
                        $ll++;
                    }
            }
            else
            {
                if(round($percentage,2) > 100)
                    {
                        $html .= '<tr>';
                        $html .= '<td>'.$ll.'</td>';
                        $html .= '<td>'.$sources->source.'</td>';
                        $html .= '<td>'.$totalSubs.'</td>';
                        $html .= '<td>'.$getDattUserTotal[0]->total.'</td>';
                        $html .= '</tr>';
                        $ll++;
                    }   
            }
       
        }
        echo $html;
    }
    public function todaySales()
    {
        $startDate = date('Y-m-01');;
        $endDate = date('Y-m-t');
        $today_sales = DB::table('leads')->whereDate('last_call', '=', date('Y-m-d'))->where('lead_status', 'sale')->select('id')->count();
        $currentMonth_sales = DB::table('leads')->whereDate('last_call', '>=', $startDate)->whereDate('last_call', '<=', $endDate)->where('lead_status', 'sale')->select('id')->count();

        return response()->json(['today_sales' => $today_sales,'currentMonth_sales' => $currentMonth_sales]);
    }

    public function getOldleads(Request $request)
    {
        if($request->p)
        {
            $pageinate = $request->p;
        }
        else
        {
            $pageinate = 25;
        }
        $date = date('Y-m-d');
        // $startDate = date('Y-m-01', strtotime("-13 months",strtotime($date)));
        // $endDate = date('Y-m-t', strtotime("-10 months",strtotime($date)));

        $startDate = '2021-07-01';
        $endDate = '2021-10-31';

        $all_packages_name = DB::table('subscriptions')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->where('subscription_type', 'PAID')->where('assigned', 0)->groupBy('package_name')->select('package_name')->get();
        if($request->package_name != "" )
        {
            $package_search = $request->package_name;
            $Oldleads = DB::table('subscriptions')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->where('subscription_type', 'PAID')->where('package_name', $request->package_name)->where('assigned', 0)->select('*')->paginate($pageinate);
        }
        else
        {
            $package_search = "";
            $Oldleads = DB::table('subscriptions')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->where('subscription_type', 'PAID')->where('assigned', 0)->select('*')->paginate($pageinate);
        }
        // $Oldleads = DB::table('subscriptions')->whereDate('valid_from', '>=', $startDate)->whereDate('valid_from', '<=', $endDate)->where('subscription_type', 'PAID')->where('assigned', 0)->select('*')->paginate(25);

        // print_r($Oldleads);die("*****");
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                            $query->orWhere('role_id', '2');
                        })
                        ->where('status', '1')
                        ->pluck('name', 'id');

        return view('backend.dashboard.planExpire', compact('Oldleads','startDate','endDate','executives','package_search','all_packages_name','pageinate'));

    }

    public function getOldleadsReSale(Request $request)
    {

        $get = 0;
        if($request->filter)
        {
            $get = $request->filter;
        }
           if($get == 1)
        {
            $resale_data = DB::select( DB::raw("SELECT u.name as executive_name, r.assi_at,s.country_code,s.user_mobile, s.user_name as customer_name ,s.package_name, s.created_at,l.lead_status FROM repitch_assigned as r INNER JOIN subscriptions as s ON s.user_mobile = r.phone INNER JOIN users as u on r.assigned_to = u.id INNER JOIN leads as l on l.data_user_id = s.datauser_id WHERE s.created_at >= r.assi_at AND s.subscription_type = 'PAID'") );

            $resale_data1 = DB::select( DB::raw("SELECT u.name as executive_name, r.assi_at,s.country_code,s.user_mobile, s.user_name as customer_name ,s.package_name, s.created_at FROM repitch_assigned as r INNER JOIN subscriptions as s ON s.user_mobile = r.phone INNER JOIN users as u on r.assigned_to = u.id WHERE s.subscription_type = 'PAID'") );

            $assignedSale_total = count($resale_data);
            $assigned_total = count($resale_data1);
        }
        else
        {
            $resale_data = DB::select( DB::raw("SELECT u.name as executive_name, r.assi_at,s.country_code,s.user_mobile, s.user_name as customer_name ,s.package_name, s.created_at,l.lead_status FROM repitch_assigned as r INNER JOIN subscriptions as s ON s.user_mobile = r.phone INNER JOIN users as u on r.assigned_to = u.id INNER JOIN leads as l on l.data_user_id = s.datauser_id WHERE s.subscription_type = 'PAID'") );

            $resale_data1 = DB::select( DB::raw("SELECT u.name as executive_name, r.assi_at,s.country_code,s.user_mobile, s.user_name as customer_name ,s.package_name, s.created_at FROM repitch_assigned as r INNER JOIN subscriptions as s ON s.user_mobile = r.phone INNER JOIN users as u on r.assigned_to = u.id WHERE s.created_at >= r.assi_at AND s.subscription_type = 'PAID'") );

            $assignedSale_total = count($resale_data1);
            $assigned_total = count($resale_data);
        }   

        return view('backend.dashboard.planExpiReresale', compact('resale_data','assigned_total','assignedSale_total'));

    }

    public function Updatesubscription(Request $request)
    {
        // print_r($request->dataid);
        foreach($request->dataid as $ids)
        {
            DB::table('subscriptions')
                ->where('id', $ids)  // find your user by their email
                ->update(
                    array(
                        'assigned' => 1,
                        'assigned_to' => $request->executive
                        )
                    );
        }
        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    
    public function saleData(Request $request)
    {

        $saleData =  DB::table('subscriptions')->select('subscriptions_tbl_id')->orderBy('id','DESC')->first();
        
        $i = 0;

        if($saleData)
        {
            $l = $saleData->subscriptions_tbl_id;
        }
        else
        {
            $l = 0;
        }
        $saleData = DB::connection('learning')
                        ->table('tbl_learning_subscriptions as s')
                        ->join('tbl_learning_learning_users as u', 'u.id', '=', 's.user_id')
                        ->leftJoin('tbl_learning_packages as p', 'p.id', '=', 's.packages_id')
                        ->where('s.id', '>',$l)
                        ->select('s.id','s.user_id','s.valid_thru','s.valid_from','s.subscription_type','s.created_at','s.updated_at','u.user_email', 'u.user_name', 'u.country_code','u.user_mobile','p.package_name')
                        ->limit(100)
                        ->get();
        // print_r($saleData);die("***");
        $i = 0;
        if($saleData)
        {
            foreach($saleData as $data)
            {
                   
                    $lead_id = DB::table('leads')->select('id','data_medium','data_user_id')->where('phone',$data->user_mobile)->first();
                    if($lead_id)
                    {
                        if($lead_id->data_medium != "")
                        {
                            $ss = $lead_id->data_medium;
                        }
                        else
                        {
                            $ss = "VARIOUS";
                        }

                        try {

                          $values = array(
                                        'subscriptions_tbl_id'     =>   $data->id,
                                        'user_id'     =>   $data->user_id,
                                        'country_code'     =>   $data->country_code,
                                        'user_mobile'     =>   $data->user_mobile,
                                        'subscription_type'     =>   $data->subscription_type,
                                        'lead_id'     =>   $lead_id->id,
                                        'datauser_id'     =>   $lead_id->data_user_id,
                                        'user_name'     =>   $data->user_name,
                                        'data_medium'     =>   $ss,
                                        'package_name'     =>   $data->package_name,
                                        'valid_from'     =>   $data->valid_from,
                                        'valid_thru'     =>   $data->valid_thru,
                                        'created_at'     =>   $data->created_at,
                                        'updated_at'     =>   $data->updated_at,
                                    );
                      
                        DB::table('subscriptions')->insert($values);
                   

                        } catch (\Exception $e) {

                            return $e->getMessage();
                        }
                        
                    }
                    else
                    {
                        $getDatasource = DB::table('data_user')->select('id','data_medium')->where('phone',$data->user_mobile)->first();
                        if($getDatasource)
                        {
                            $du_id = $getDatasource->id;
                            $ss = $getDatasource->data_medium;
                        }
                        else
                        {
                            try {

                                $du = array(
                                        'name'     =>   $data->user_name,
                                        'email'     =>   $data->user_email,
                                        'country_code'     =>   $data->country_code,
                                        'phone'     =>   $data->user_mobile,
                                        'data_medium'     =>  'VARIOUS',
                                        'created_at' => $data->created_at,
                                        'updated_at' => $data->updated_at,
                                 );
                                $du_id = DB::table('data_user')->insertGetId($du);
                       

                            } catch (\Exception $e) {

                                return $e->getMessage();
                            }

                            $ss = "VARIOUS";
                        }

                        try {

                                $values = array(
                                    'subscriptions_tbl_id'     =>   $data->id,
                                    'user_id'     =>   $data->user_id,
                                    'country_code'     =>   $data->country_code,
                                    'user_mobile'     =>   $data->user_mobile,
                                    'subscription_type'     =>   $data->subscription_type,
                                    'user_name'     =>   $data->user_name,
                                    'phone_not_found'     =>   $data->user_mobile,
                                    'data_medium'     =>  $ss,
                                    'datauser_id'     =>   $du_id,
                                    'package_name'     =>   $data->package_name,
                                    'valid_from'     =>   $data->valid_from,
                                    'valid_thru'     =>   $data->valid_thru,
                                    'created_at'     =>   $data->created_at,
                                    'updated_at'     =>   $data->updated_at,
                                );
                        

                        DB::table('subscriptions')->insert($values);
                       

                            } catch (\Exception $e) {

                                return $e->getMessage();
                            }


                    }
                    $l = $data->id;
                    $i++;

                }

            }
     
    }

    //      public function saleData(Request $request)
    // {
    //     if($request->has('last_id') && $request['last_id']!="")
    //     {
    //         $l = $request['last_id'];
    //     }
    //     else
    //     {
    //         $l = 0;
    //     }

    //     $saleData =  DB::connection('learning')
    //                     ->table('tbl_learning_subscriptions as s')
    //                     ->join('tbl_learning_learning_users as u', 'u.id', '=', 's.user_id')
    //                     ->leftJoin('tbl_learning_packages as p', 'p.id', '=', 's.packages_id')
    //                     ->where('s.id', '>',$l)
    //                     // ->where('s.subscription_status', 'ACTIVE')
    //                     ->select('s.id','s.user_id','s.valid_thru','s.valid_from','s.subscription_type','s.created_at','s.updated_at','u.user_email', 'u.user_name', 'u.country_code','u.user_mobile','p.package_name')
    //                     ->limit(1000)
    //                     ->get();
    //     $i = 0;
    //     // die("*******");
    //     foreach($saleData as $data)
    //     {
           
    //         $lead_id = DB::table('leads')->select('id','data_medium','data_user_id')->where('phone',$data->user_mobile)->first();
    //             if($lead_id)
    //             {
    //              $insert = DB::table('subscriptions_copy')->insert(
    //                      array(
    //                             'subscriptions_tbl_id'     =>   $data->id,
    //                             'user_id'     =>   $data->user_id,
    //                             'country_code'     =>   $data->country_code,
    //                             'user_mobile'     =>   $data->user_mobile,
    //                             'subscription_type'     =>   $data->subscription_type,
    //                             'lead_id'     =>   $lead_id->id,
    //                             'datauser_id'     =>   $lead_id->data_user_id,
    //                             'user_name'     =>   $data->user_name,
    //                             'data_medium'     =>   $lead_id->data_medium,
    //                             'package_name'     =>   $data->package_name,
    //                             'valid_from'     =>   $data->valid_from,
    //                             'valid_thru'     =>   $data->valid_thru,
    //                             'created_at'     =>   $data->created_at,
    //                             'updated_at'     =>   $data->updated_at,
    //                      )
    //                 );
    //             }
    //             else
    //             {
    //                  $getDatasource = DB::table('data_user_copy')->select('id','data_medium')->where('phone',$data->user_mobile)->first();
    //                 if($getDatasource)
    //                 {
    //                     $du_id = $getDatasource->id;
    //                     $ss = $getDatasource->data_medium;
    //                 }
    //                 else
    //                 {
    //                      $du_id = $insert = DB::table('data_user_copy')->insertGetId(
    //                              array(
    //                                     'name'     =>   $data->user_name,
    //                                     'email'     =>   $data->user_email,
    //                                     'country_code'     =>   $data->country_code,
    //                                     'phone'     =>   $data->user_mobile,
    //                                     'data_medium'     =>  'VARIOUS',
    //                                     'created_at' => $data->created_at,
    //                                     'updated_at' => $data->updated_at,
    //                              )
    //                             );
    //                     $ss = "VARIOUS";
    //                 }
    //                 $insert = DB::table('subscriptions_copy')->insert(
    //                      array(
    //                             'subscriptions_tbl_id'     =>   $data->id,
    //                             'user_id'     =>   $data->user_id,
    //                             'country_code'     =>   $data->country_code,
    //                             'user_mobile'     =>   $data->user_mobile,
    //                             'subscription_type'     =>   $data->subscription_type,
    //                             'user_name'     =>   $data->user_name,
    //                             'phone_not_found'     =>   $data->user_mobile,
    //                             'data_medium'     =>  $ss,
    //                             'datauser_id'     =>   $du_id,
    //                             'package_name'     =>   $data->package_name,
    //                             'valid_from'     =>   $data->valid_from,
    //                             'valid_thru'     =>   $data->valid_thru,
    //                             'created_at'     =>   $data->created_at,
    //                             'updated_at'     =>   $data->updated_at,
    //                      )
    //                 );
    //             }
    //             $l = $data->id;
    //          if($insert)
    //          {
    //             echo "<p style='color:green'>".$i."st record inserted</p>";
    //          }
    //          else
    //          {
    //             echo "<p style='color:red'>".$i."st record not inserted </p>";
    //          }
    //      $i++;
    //     }
    //     redirect()->to('https://crm.classmonitor.com/admin/reports/saleData?last_id='.$l.'$du='.$du_id)->send();
    // }

   
    public function subscriptions()
    {

        
        
         // $startDate = date("Y-m-d", strtotime("-29 day"));

         //    $Deliteleads = DB::table('subscriptions')->whereDate('created_at', '=', $startDate)->where('subscription_type', 'PAID')->orderBy('created_at','ASC')->pluck('datauser_id');
         //    print_r($Deliteleads);
         //    die("****");
         $startDate = date("Y-m-d", strtotime("-29 day"));

            $Deliteleads = DB::table('subscriptions')->whereDate('created_at', '=', $startDate)->where('subscription_type', 'PAID')->orderBy('created_at','ASC')->pluck('datauser_id');
            // print_r($Deliteleads);die("***");
            $dataUserId = "";
            $executive = 140;
            $callDate = date('Y/m/d');
            $assignToMass = 'true';
            $ids = $Deliteleads;

            $arr = explode('/', $callDate);
            $callDate = $arr[2].'-'.$arr[1].'-'.$arr[0];

            $user = User::findOrFail($executive);

            DB::transaction(function () use ($user, $dataUserId, $callDate, $assignToMass, $ids) {
                
                $data = DataUser::whereIn('id', $ids)->get();    
                if($data->count() > 0)
                {
                    foreach($data as $row)
                    {
                        DataUser::where('id', $row->id)->update(['moved_to_lead' => '1', 'lead_status' => 'open', 'assigned_to' => $user->id, 'updated_at' => $row->updated_at]);
                        $lead = Lead::where('country_code', $row->country_code)->where('phone', $row->phone)->first();
                        if(!$lead)
                        {
                            $lead = new Lead;
                            $lead->data_user_id      = $row->id;
                            $lead->name              = $row->name;
                            $lead->email             = $row->email;
                            $lead->country_code      = $row->country_code;
                            $lead->phone             = $row->phone;
                            $lead->messenger         = $row->messenger;
                            $lead->messenger_id      = $row->messenger_id;
                            $lead->learning          = $row->learning;
                            $lead->learning_id       = $row->learning_id;
                            $lead->community         = $row->community;
                            $lead->community_id      = $row->community_id;
                            $lead->login_id          = $row->login_id;
                            $lead->status            = $row->status;
                            $lead->lat_long          = $row->lat_long;
                            $lead->locality          = $row->locality;
                            $lead->city              = $row->city;
                            $lead->state             = $row->state;
                            $lead->country           = $row->country;
                            $lead->data_medium       = $row->data_medium;
                            $lead->email_verified    = $row->email_verified;
                            $lead->last_activity     = $row->last_activity;
                            $lead->registered_on     = $row->registered_on;
                            $lead->assigned_to       = $user->id;
                            $lead->lead_stage        = $row->lead_stage;
                            $lead->call_date         = $callDate;
                            $lead->save();
        
                            event(new LeadAssigned($lead, $user));
                        }else{
                            $oldUser = User::find($lead->assigned_to);

                            $lead->done = '0';
                            $lead->assigned_to = $user->id;
                            $lead->lead_status = 'open';
                            $lead->assigned_type = 'transferred';
                            $lead->update();
                        }
                    }

                    return true;
                }
            });
            echo "done";
        // $subscriptions = DB::connection('learning')
        //                 ->table(config('table.learning.subscriptions').' as s')
        //                 ->leftJoin(config('table.learning.children').' as c', 'c.id', '=', 's.child_id')
        //                 ->leftJoin(config('table.learning.packages').' as p', 'p.id', '=', 's.packages_id')
        //                 ->leftJoin(config('table.learning.package_orders').' as po', 'po.order_id', '=', 's.order_id')
        //                 // ->where('s.user_id', 88306)
        //                 // ->where('s.subscription_status', 'ACTIVE')
        //                 ->select('s.alias', 'p.package_name', 's.subscription_type', 'po.package_addons_id', 'c.child_name', 'c.child_class', 's.created_at')
        //                 ->get();
        // print_r($subscriptions);
        // $subscriptions = DB::connection('learning')
        //                 ->table(config('table.learning.subscriptions')' as s')
        //                 ->innerJoin(config('table.learning.users')' as u', 'u.id', '=', 's.user_id')
        //                 // ->where('s.subscription_type', 'PAID')
        //                 ->select('s.*', 'u.*')
        //                 ->get();
    }
}
