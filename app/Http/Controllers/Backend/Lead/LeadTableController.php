<?php

namespace App\Http\Controllers\Backend\Lead;

use Illuminate\Http\Request;
use App\Models\Access\User\User;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Backend\Lead\LeadRepository;

/**
 * Class LeadTableController.
 */
class LeadTableController extends Controller
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
     * @param Request $request
     *
     * @return mixed
     */
    public function callList(Request $request)
    {
        return Datatables::of($this->leads->getForDataTable($request, '0'))
            ->editColumn('lead_status', function ($lead) {
                return '<button class="btn '.$lead->status_class.' btn-sm">'.leadStatus($lead->lead_status).'</button>';
            })
            ->editColumn('last_call', function ($lead) {
                return ($lead->last_call != null)? $lead->last_call->format(config('access.date_time_format')) : '';
            })
            ->editColumn('phase', function ($row) {
                if($row->phase == 'buy_attempt')
                    return 'Buy Attempt';
                else if($row->phase == 'cart')
                    return 'Cart Abandon';
                else if($row->phase == 'trial')
                    return 'Trial Started';
                else if($row->phase == 'kit_purchased')
                    return 'Kit Purchased';
                else
                    return '';
            })
            ->editColumn('next_follow_up', function ($lead) {
                return ($lead->next_follow_up != null)? $lead->next_follow_up->format(config('access.date_time_format')) : '';
            })
            ->rawColumns(['lead_status'])
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function followUpList(Request $request)
    {
        return Datatables::of($this->leads->getFollowUpForDataTable($request, '0'))
            ->editColumn('lead_status', function ($lead) {
                return '<button class="btn '.$lead->status_class.' btn-sm">'.leadStatus($lead->lead_status).'</button>';
            })
            ->editColumn('last_call', function ($lead) {
                return ($lead->last_call != null)? $lead->last_call->format(config('access.date_time_format')) : '';
            })
            ->editColumn('phase', function ($row) {
                if($row->phase == 'buy_attempt')
                    return 'Buy Attempt';
                else if($row->phase == 'cart')
                    return 'Cart Abandon';
                else if($row->phase == 'trial')
                    return 'Trial Started';
                else if($row->phase == 'kit_purchased')
                    return 'Kit Purchased';
                else
                    return '';
            })
            ->editColumn('next_follow_up', function ($lead) {
                if($lead->next_follow_up != null) {
                    if(time() > strtotime($lead->next_follow_up)){
                        return '<span class="text-danger">'.$lead->next_follow_up->format(config('access.date_time_format')).'</span>';
                    }else{
                        return $lead->next_follow_up->format(config('access.date_time_format'));
                    }
                }else{
                    return '';
                }
            })
            ->rawColumns(['lead_status', 'next_follow_up'])
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function leadList(Request $request)
    {
        return Datatables::of($this->leads->getLeadsForDataTable($request))
            ->editColumn('lead_status', function ($lead) {
                return '<button class="btn '.$lead->status_class.' btn-sm">'.leadStatus($lead->lead_status).'</button>';
            })
            ->editColumn('last_call', function ($lead) {
                return ($lead->last_call != null)? $lead->last_call->format(config('access.date_time_format')) : '';
            })
            ->editColumn('last_updated', function ($lead) {
                return ($lead->updated_at != null)? $lead->updated_at->format(config('access.date_time_format')) : '';
            })
            ->addColumn('assigned_to', function ($lead) {
                return ($lead->assigned_executive)? $lead->assigned_executive->name:'';
            })
            ->editColumn('phase', function ($row) {
                if($row->phase == 'buy_attempt')
                    return 'Buy Attempt';
                else if($row->phase == 'cart')
                    return 'Cart Abandon';
                else if($row->phase == 'trial')
                    return 'Trial Started';
                else if($row->phase == 'kit_purchased')
                    return 'Kit Purchased';
                else
                    return '';
            })
            ->editColumn('next_follow_up', function ($lead) {
                if($lead->next_follow_up != null) {
                    if(time() > strtotime($lead->next_follow_up)){
                        return '<span class="text-danger">'.$lead->next_follow_up->format(config('access.date_time_format')).'</span>';
                    }else{
                        return $lead->next_follow_up->format(config('access.date_time_format'));
                    }
                }else{
                    return '';
                }
            })
            ->rawColumns(['lead_status', 'next_follow_up'])
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function transferredList(Request $request)
    {
        return Datatables::of($this->leads->getTransferredLeadsForDataTable($request))
            ->editColumn('lead_status', function ($lead) {
                return '<button class="btn '.$lead->status_class.' btn-sm">'.leadStatus($lead->lead_status).'</button>';
            })
            ->editColumn('last_call', function ($lead) {
                return ($lead->last_call != null)? $lead->last_call->format(config('access.date_time_format')) : '';
            })
            ->editColumn('next_follow_up', function ($lead) {
                return ($lead->next_follow_up != null)? $lead->next_follow_up->format(config('access.date_time_format')) : '';
            })
            ->rawColumns(['lead_status'])
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function calledList(Request $request)
    {
        return Datatables::of($this->leads->getCallHistoryForDataTable($request))
            ->editColumn('lead_status', function ($call) {
                $html = '<button class="btn '.$call->lead->status_class.' btn-xs">'.leadStatus($call->lead_status).'</button>';
                return $html;
            })
            ->editColumn('name', function ($call) {
                return $call->lead->name;
            })
            ->editColumn('lead_id', function ($call) {
                return $call->lead->id;
            })
            ->addColumn('call_duration', function ($call) {
                $html = '';

                if($call->exotel_sid != null && $call->exotel_sid != ''){
                    $html .= duration($call->duration, true);
                    if($call->call_record_file != null)
                    {
                        $html .= '<audio id="audio-'.$call->id.'" src="'.$call->call_record_file.'" class="hide"></audio>';
                        $html .= '&nbsp; &nbsp;<button class="btn btn-success btn-xs recordPlay" data-val="'.$call->id.'">Play</button>';
                    }
                }else if($call->call_record_file != null)
                {
                    $html .= duration($call->duration);
                    $html .= '<audio id="audio-'.$call->id.'" src="'.url('storage/call_records/'.$call->call_record_file).'" class="hide"></audio>';
                    $html .= '&nbsp; &nbsp;<button class="btn btn-success btn-xs recordPlay" data-val="'.$call->id.'">Play</button>';
                }else{
                    $html .= duration($call->duration);
                }
                
                return $html;
            })
            ->addColumn('called_at', function ($call) {
                return ($call->created_at != null)? $call->created_at->format(config('access.date_time_format')) : '';
            })
            ->editColumn('next_follow_up', function ($call) {
                return ($call->next_follow_up != null)? $call->next_follow_up->format(config('access.date_time_format')) : '';
            })
            ->rawColumns(['lead_status', 'call_duration'])
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function calledManagerList(Request $request)
    {
        $total = $this->leads->getCalledListForManagerCount($request);
        return Datatables::of($this->leads->getCalledListForManager($request))
            ->editColumn('lead_status', function ($call) {
                $html = '<button class="btn '.$call->lead_status.' btn-xs">'.leadStatus($call->lead_status).'</button>';
                return $html;
            })
            ->editColumn('name', function ($call) {
                return $call->name;
            })
            ->editColumn('data_user_id', function ($call) {
                return $call->data_user_id;
            })
            ->editColumn('lead_id', function ($call) {
                return $call->lead_id;
            })
            ->editColumn('frequency', function ($call) {
                return $call->frequency;
            })
            ->editColumn('data_medium', function ($call) {
                return $call->data_medium;
            })
            ->addColumn('call_duration', function ($call) {
                $html = '';
                if($call->exotel_sid != null && $call->exotel_sid != ''){
                    $html .= duration($call->duration, true);
                    if($call->call_record_file != null)
                    {
                        $html .= '<audio id="audio-'.$call->id.'" src="'.$call->call_record_file.'" class="hide"></audio>';
                        $html .= '&nbsp; &nbsp;<button class="btn btn-success btn-xs recordPlay" data-val="'.$call->id.'">Play</button>';
                    }
                }else if($call->call_record_file != null)
                {
                    $html .= duration($call->duration);
                    $html .= '<audio id="audio-'.$call->id.'" src="'.url('storage/call_records/'.$call->call_record_file).'" class="hide"></audio>';
                    $html .= '&nbsp; &nbsp;<button class="btn btn-success btn-xs recordPlay" data-val="'.$call->id.'">Play</button>';
                }else{
                    $html .= duration($call->duration);
                }
                return $html;
            })
            ->addColumn('called_by', function ($call) {
                // $user = User::find($call->called_by);
                // return $user->name;
                return $call->called_by_name;
            })
            ->addColumn('assigned_to', function ($call) {
                // $user = User::find($call->assigned_to);
                // return $user->name;
                return $call->assigned_to_name;
            })
            ->editColumn('created_at', function ($call) {
                return ($call->created_at != null)? date('d M h:i A', strtotime($call->created_at)) : '';
            })
            ->editColumn('next_follow_up', function ($call) {
                return ($call->next_follow_up != null)? date(config('access.date_time_format'), strtotime($call->next_follow_up)) : '';
            })
            ->rawColumns(['lead_status', 'call_duration'])
            ->setTotalRecords($total)
            ->make(true);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function searchLead(Request $request)
    {
        return Datatables::of($this->leads->leadSearch($request))
            ->editColumn('lead_status', function ($lead) {
                $html = '<button class="btn '.$lead->status_class.' btn-xs">'.leadStatus($lead->lead_status).'</button>';
                return $html;
            })
            ->editColumn('name', function ($lead) {
                return $lead->name;
            })
            ->editColumn('alternate_number', function ($lead) {
                $numbers = $lead->alternateNumbers()->pluck('phone')->toArray();
                return implode(',', $numbers);
            })
            ->addColumn('assigned_to', function ($lead) {
                return ($lead->assigned_executive)? $lead->assigned_executive->name:'';
            })
            ->editColumn('last_call', function ($lead) {
                return ($lead->last_call != null)? $lead->last_call->format(config('access.date_time_format')) : '';
            })
            ->editColumn('next_follow_up', function ($lead) {
                return ($lead->next_follow_up != null)? $lead->next_follow_up->format(config('access.date_time_format')) : '';
            })
            ->addColumn('action', function ($lead) {
                return '<button class="btn btn-xs assignedTo" data-val="'.$lead->id.'">Assigned To</button>';
            })
            ->rawColumns(['lead_status', 'action'])
            ->make(true);
    }
}
