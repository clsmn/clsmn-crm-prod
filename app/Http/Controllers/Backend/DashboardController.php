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

    public function leadPerformance(Request $request)
    {
        $data = Lead::whereNotNull('data_medium')
                ->where('data_medium','!=' ,'')
                ->groupBy('data_medium')
                ->select([
                    'data_medium', 
                    DB::raw('count(id) as totalLeads'),
                    DB::raw('(SELECT count(l.id) FROM leads as l WHERE l.data_medium=leads.data_medium AND l.lead_status="sale") as totalSales')
                ])
                ->get();
                
        return view('backend.dashboard.leadPerformance', compact('data'));
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
