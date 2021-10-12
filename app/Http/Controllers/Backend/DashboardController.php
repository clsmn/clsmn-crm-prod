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

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @var LeadRepository
     */
    protected $leads;

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
                    $arr['name'] = $executive->name;
                    $arr['id'] = $executive->id;
                    $arr['sale'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'sale')
                                            ->count();
                    $arr['hot'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'hot')
                                            ->count();
                    $arr['mild'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'mild')
                                            ->count();
                    $arr['cold'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'cold')
                                            ->count();
                    $arr['no_answer'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'no_answer')
                                            ->count();
                    $arr['busy'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'busy')
                                            ->count();
                    $arr['not_interested'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'not_interested')
                                            ->count();
                    $arr['dead'] = CallHistory::where('called_by', $executive->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('lead_status', 'dead')
                                            ->count();

                    $tableData[] = $arr;
                }
            }

            $data = $this->leads->managerDashboardStats($startDate, $endDate);
            return view('backend.dashboard.manager', compact('data', 'startDate', 'endDate', 'pickerStartDate', 'pickerEndDate', 'tableData'));
        }elseif($user->hasRole('Executive'))
        {   
            $data = $this->leads->executiveDashboardStats($user, $startDate, $endDate);
            return view('backend.dashboard.executive', compact('data', 'startDate', 'endDate', 'pickerStartDate', 'pickerEndDate'));
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
        
        $data = DB::table('upload_datas')
                ->where('date',date('Y-m-d'))
                ->groupBy('data_medium')
                ->select('data_medium as source')
                ->get();

        $i = 0;
        $j = 0;
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
                                DB::raw("(SELECT count(*) FROM upload_datas
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
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).' <b><small>('.$lead_perform->total_source.')</small></b></a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->repeatlLeads.'</a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->newLeads.'</a></td></tr>';
        }
        $html .= '</tbody>';
        }
        else
        {
            $html .='<tr><td style="text-align:center">No results found.</td> </tr></tbody>';  
        }
        // echo $html;die("*****");
        return view('backend.dashboard.leadUpload', compact('html'));
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
                                DB::raw("(SELECT count(*) FROM upload_datas
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
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.($lead_perform->repeatlLeads + $lead_perform->newLeads).' <b><small>('.$lead_perform->total_source.')</small></b></a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->repeatlLeads.'</a></td>';
            $html .= '<td><a href="javascript:void(0)" onclick="getUploadDetail('.$variable.')">'.$lead_perform->newLeads.'</a></td></tr>';
        }
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
                            <th>Total Leads</th>
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
            $html .= '<tr><td>'.$value->source.'</td><td>'.$value->totalLeads.'</td>';
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
                      ->select('data_medium',DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND (leads.lead_status = 'open' || leads.lead_status = '')
                                    ) as open"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'hot'
                                    ) as hot"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'mild'
                                    ) as mild"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'cold'
                                    ) as cold"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'dead'
                                    ) as dead"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'sale'
                                    ) as sale"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'no_answer'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$values->source."' AND leads.lead_status = 'busy'
                                    ) as busy"),
                               DB::raw("(SELECT count(*) FROM leads
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
                            <th>Total Leads</th>
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
            $html .= '<tr><td>'.$value->source.'</td><td>'.$value->totalLeads.'</td>';
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
        $data = DB::table('leads')
                ->where('created_at','>=',$request->startDate)
                ->where('created_at','<=',$request->endDate)
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
        $html = '<thead>
                <tr>
                    <th>Lead Source</th>
                    <th>Total Leads</th>
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
           
              $lead_perform = \DB::table("leads")
                      ->select('data_medium',DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as open"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'hot' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as hot"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'mild' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as mild"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'cold' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as cold"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'dead' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as dead"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'sale' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as sale"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'no_answer' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'busy' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as busy"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value->source."' AND leads.lead_status = 'not_interested' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as not_interested")
                              )
                      ->first();
            if($lead_perform->totalLeads > 0)
            {
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
                
                $html .= '<tr class="common_source_class" id="source_id_'.$value->source.'"><td>'.$value->source.'</td>';
                $html .= '<td>'.$lead_perform->totalLeads.'</td>';
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
                    <th>Total Leads</th>
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
              if($request->startDate == 'undefined' && $request->endDate == 'undefined')
              {
                $lead_perform = \DB::table("leads")
                      ->select('data_medium',DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '')
                                    ) as open"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot'
                                    ) as hot"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild'
                                    ) as mild"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold'
                                    ) as cold"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead'
                                    ) as dead"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale'
                                    ) as sale"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy'
                                    ) as busy"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested'
                                    ) as not_interested")
                              )
                      ->first();
              } 
              else
              { 
              $lead_perform = \DB::table("leads")
                      ->select('data_medium',DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."'
                                    ) as totalLeads"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND (leads.lead_status = 'open' || leads.lead_status = '') AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as open"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'hot' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as hot"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'mild' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as mild"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'cold' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as cold"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'dead' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as dead"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'sale' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as sale"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'no_answer' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as no_answer"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'busy' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as busy"),
                               DB::raw("(SELECT count(*) FROM leads
                                      WHERE leads.data_medium = '".$value."' AND leads.lead_status = 'not_interested' AND leads.last_call >= '".$request->startDate."' AND leads.last_call <= '".$request->endDate."'
                                    ) as not_interested")
                              )
                      ->first();
                }
            if($lead_perform->totalLeads > 0)
            {
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
            $html .= '<tr class="common_source_class" id="source_id_'.$value.'"><td>'.$value.'</td>';
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
}
