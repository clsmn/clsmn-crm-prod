<?php

namespace App\Http\Controllers\Backend\Workforce;

use App\Models\DailyTrack;
use Illuminate\Http\Request;
use App\Models\Access\User\User;
use App\Models\Lead\CallHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Access\User\UserRepository;

/**
 * Class WorkforceController.
 */
class WorkforceController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $executiveId = $request->get('executive');
        $checkInOut = false;
        
        $startDate = ($startDate != NULL & $startDate != '')? $startDate : date('Y-m-d');
        $endDate = ($endDate != NULL & $endDate != '')? $endDate : date('Y-m-d');
        if($startDate == $endDate)
        {
            $checkInOut = true;
        }

        $query = DB::table(config('access.call_history_table').' as ch')
                        ->leftJoin(config('access.users_table').' as u', 'u.id', '=', 'ch.called_by')
                        ->whereRaw(DB::raw('DATE(ch.created_at) >= "'.$startDate.'" AND DATE(ch.created_at) <= "'.$endDate.'"'))
                        ->where('ch.saved', '1')
                        ->select(DB::raw('SUM(ch.duration) as total_call_time, COUNT(ch.id) as calls, u.id, u.name'));
        if($executiveId != null && $executiveId != '')
        {
            $query->where('ch.called_by', $executiveId);
        }
        $data = $query->groupBy('ch.called_by')
                        ->get();
        
        $tableData = $users = array();
        $totalCallMinutes = '0';
        $executivesQuery = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                            $query->orWhere('role_id', '2');
                        })
                        ->where('status', '1')
                        ->select('name', 'id', 'note');
        if($executiveId != null && $executiveId != '')
        {
            $executivesQuery->where('id', $executiveId);
        }
        $executives = $executivesQuery->get();

        if($executives->count() > 0)
        {
            foreach($executives as $executive)
            {
                $users[$executive->id] = $executive->name;

                $row = $data->where('id', $executive->id)->first();
                if($row != null)
                {
                    $arr['name'] = $row->name;
                    $arr['id'] = $row->id;
                    $arr['calls'] = $row->calls;
                    $arr['call_time'] = ($row->total_call_time == null)? '0': round(($row->total_call_time/1000)/60);
                    $totalCallMinutes += $arr['call_time'];

                    $data_work = DB::select("SELECT `lead_status`, COUNT(*) as total FROM `call_history` WHERE DATE(`created_at`) >= '".$startDate."' AND DATE(`created_at`) <= '".$endDate."' AND `called_by` = ".$executive->id." AND `saved` = '1' GROUP BY `lead_status`");

                    $arr['sale'] = 0;
                    $arr['hot'] = 0;
                    $arr['mild'] = 0;
                    $arr['cold'] = 0;
                    $arr['no_answer'] = 0;
                    $arr['busy'] = 0;
                    $arr['not_interested'] = 0;
                    $arr['dead'] = 0;
                    foreach($data_work as $vv)
                    {
                       $arr[$vv->lead_status] = $vv->total; 
                    }
                    // $arr['name'] = $row->name;
                    // $arr['id'] = $row->id;
                    // $arr['calls'] = $row->calls;
                    // $arr['call_time'] = ($row->total_call_time == null)? '0': round(($row->total_call_time/1000)/60);
                    // $totalCallMinutes += $arr['call_time'];
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
                }else{
                    $arr['name'] = $executive->name;
                    $arr['id'] = $executive->id;
                    $arr['calls'] = '0';
                    $arr['call_time'] = '0';
                    $arr['sale'] = '0';
                    $arr['hot'] = '0';
                    $arr['mild'] = '0';
                    $arr['cold'] = '0';
                    $arr['no_answer'] = '0';
                    $arr['busy'] = '0';
                    $arr['not_interested'] = '0';
                    $arr['dead'] = '0';
                }
                $arr['note'] = $executive->note;

                //add Check-in & check-out
                if($checkInOut)
                {
                    $track = DailyTrack::where('user_id', $executive->id)
                                    ->where('date', $startDate)
                                    ->first();
                    if($track != null)
                    {
                        $arr['check_in'] = ($track->in != null)? $track->in : 'NA';
                        $arr['check_out'] = ($track->out != null)? $track->out : 'NA';
                    }else{
                        $arr['check_in'] = 'NA';
                        $arr['check_out'] = 'NA';
                    }
                }else{
                    $arr['check_in'] = 'NA';
                    $arr['check_out'] = 'NA';
                }

                $tableData[] = $arr;
            }
        }
        $executives = $users;
        $pickerStartDate = date('m/d/Y', strtotime($startDate));
        $pickerEndDate = date('m/d/Y', strtotime($endDate));

        return view('backend.workforce.index', compact('executives', 'tableData', 'totalCallMinutes', 'startDate', 'endDate', 'executiveId', 'pickerStartDate', 'pickerEndDate'));
    }

    public function executive(User $user)
    {
        return view('backend.access.show')
                ->withUser($user);
    }

    function setExecutiveNote(Request $request)
    {
        $dataUserId = $request->get('dataUserId');
        $executiveNote = $request->get('executiveNote');

        $user = User::findOrFail($dataUserId);
        $user->note = $executiveNote;
        $user->update();

        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }
}