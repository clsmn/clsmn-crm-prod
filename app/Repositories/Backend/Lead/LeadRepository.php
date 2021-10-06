<?php

namespace App\Repositories\Backend\Lead;

use App\Models\Lead\Lead;
use App\Models\Lead\LeadNote;
use App\Events\Backend\Lead\LeadNoteAdded;
use App\Models\DailyTrack;
use App\Models\Data\DataUser;
use App\Models\Data\DataChild;
use App\Models\Lead\CallHistory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use App\Events\Backend\Lead\LeadAssigned;
use App\Events\Backend\Lead\LeadChangeNumber;
use App\Events\Backend\Access\User\UserCheckIn;
use App\Models\AlternateNumber\AlternateNumber;
use App\Events\Backend\Access\User\UserCheckOut;
use App\Events\Backend\Access\User\UserFetchLeads;

/**
 * Class LeadRepository.
 */
class LeadRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Lead::class;

    function assignLeads($user, $date = NULL)
    {
        $date = ($date == NULL)? date('Y-m-d') : $date;

        //get already assigned leads count
        $assignedLeadsCount = $this->query()->where('assigned_to', $user->id)
                                        ->where('call_date', $date)
                                        ->count();
        $leadsToBeAssigned = config('access.per_day_leads') - $assignedLeadsCount;
        //$leadsToBeAssigned = 1;
        if($leadsToBeAssigned > 0)
        {
            // assign leads from data pool.
            DB::transaction(function () use ($user, $leadsToBeAssigned, $date) {
                $data = DataUser::where('moved_to_lead', '0')->orderBy('created_at','DESC')->limit($leadsToBeAssigned)->get();
                //$data = DataUser::where('id', '38')->limit($leadsToBeAssigned)->get();
                if($data->count() > 0)
                {
                    //update moved_to_lead in data pool.
                    $dataIds = $data->pluck('id');
                    DataUser::whereIn('id', $dataIds)->update(['moved_to_lead' => '1']);

                    foreach($data as $row)
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
                        $lead->lead_stage        = ($row->data_medium == 'messenger' || $row->data_medium == 'login')? '1' : NULL;
                        $lead->call_date         = $date;
                        $lead->save();

                        event(new LeadAssigned($lead, $user));
                    }

                    event(new UserFetchLeads($user));
                }
            });
        }
    }

    function userDailyTrack($user, $type)
    {
        if(!$user->hasRole('Administrator'))
        {
            $date = date('Y-m-d');
            $track = DailyTrack::where('user_id', $user->id)->where('date', $date)->first();
    
            if($track == null)
            {
                $track = new DailyTrack;
                $track->user_id = $user->id;
                $track->date    = $date;
                $track->in      = null;
                $track->out     = null;
                $track->save();
            }
    
            if($track->in == null && $type == 'in')
            {
                // assign leads
                //$this->assignLeads($user); //No lead will be assigned on check in.
    
                $track->in = date('Y-m-d H:i:s');
                $track->update();
    
                event(new UserCheckIn($user));
            }
    
            if($track->out == null && $type == 'out')
            {
                $track->out = date('Y-m-d H:i:s');
                $track->update();
    
                event(new UserCheckOut($user));
            }
        }
    }

    function getForDataTable($request, $done='0')
    {
        $user = $request->user();
        $subscriptionType = $request->get('subscription_type');
        $dataMedium = $request->get('medium');
        $city = $request->get('city');
        $phase = $request->get('phase');
        $type = $request->get('type');
        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->where('done', $done)
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
            ])
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'ASC');

        if($city != '' && $city != NULL)
        {
            $dataTableQuery->where('city', $city);
        }
        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('phase', $phase);
        }
        if($dataMedium != '' && $dataMedium != NULL)
        {
            $dataTableQuery->where('data_medium', $dataMedium);
        }
        if($type == 'new')
        {
            $dataTableQuery->whereNull('next_follow_up');
        }else if($type == 'followUp')
        {
            $dataTableQuery->whereNotNull('next_follow_up');
        }else if($type != '')
        {
            $dataTableQuery->where('lead_status', $type);
        }
        // if($subscriptionType != 'all')
        // {
        //     if($subscriptionType != 'NULL')
        //     {
        //         $dataTableQuery->where('subscription_type', $subscriptionType);
        //     }else
        //     {
        //         $dataTableQuery->whereNull('subscription_type');
        //     }
        // }
        //$dataTableQuery->orderBy('updated_at', 'DESC');
        return $dataTableQuery;
    }

    function getFollowUpForDataTable($request, $done='0')
    {
        $user = $request->user();
        $subscriptionType = $request->get('subscription_type');
        $dataMedium = $request->get('medium');
        $phase = $request->get('phase');
        $type = $request->get('type');
        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->where('done', $done)
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
            ])
            ->orderBy('next_follow_up', 'ASC')
            ->orderBy('id', 'ASC');

        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('phase', $phase);
        }
        if($dataMedium != '' && $dataMedium != NULL)
        {
            $dataTableQuery->where('data_medium', $dataMedium);
        }
        $dataTableQuery->whereNotNull('next_follow_up');
        if($type != '')
        {
            $dataTableQuery->where('lead_status', $type);
        }
        return $dataTableQuery;
    }

    function getLeadsForDataTable($request)
    {
        $assignedTo = $request->get('assignedTo');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $subscriptionType = $request->get('subscription_type');
        $dataMedium = $request->get('medium');
        $city = $request->get('city');
        $phase = $request->get('phase');
        $type = $request->get('type');
        $dataTableQuery = $this->query()
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.data_user_id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
                config('access.leads_table').'.assigned_to',
                config('access.leads_table').'.updated_at',
            ])
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'ASC');

        if($assignedTo != '' && $assignedTo != NULL)
        {
            $dataTableQuery->where('assigned_to', $assignedTo);
        }
        if($city != '' && $city != NULL)
        {
            $dataTableQuery->where('city', $city);
        }
        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('phase', $phase);
        }
        if($dataMedium != '' && $dataMedium != NULL)
        {
            $dataTableQuery->where('data_medium', $dataMedium);
        }
        if($type == 'new')
        {
            $dataTableQuery->whereNull('next_follow_up');
        }else if($type == 'followUp')
        {
            $dataTableQuery->whereNotNull('next_follow_up');
        }else if($type != '')
        {
            $dataTableQuery->where('lead_status', $type);
            
        }
        if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
        {
            $dataTableQuery->whereDate('last_call', '>=', $startDate);
            $dataTableQuery->whereDate('last_call', '<=', $endDate);
        }
        // if($subscriptionType != 'all')
        // {
        //     if($subscriptionType != 'NULL')
        //     {
        //         $dataTableQuery->where('subscription_type', $subscriptionType);
        //     }else
        //     {
        //         $dataTableQuery->whereNull('subscription_type');
        //     }
        // }
        //$dataTableQuery->orderBy('updated_at', 'DESC');
        return $dataTableQuery;
    }

    function getTransferredLeadsForDataTable($request)
    {
        $user = $request->user();

        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->where('assigned_type', 'transferred')
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.lead_status',
            ]);
        //$dataTableQuery->orderBy('next_follow_up', 'ASC');
        return $dataTableQuery;
    }

    function getCallHistoryForDataTable($request)
    {
        $user = $request->user();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $leadStatus = $request->get('lead_status');
        $medium = $request->get('medium');
        $city = $request->get('city');
        $search = $request->get('search');

        $query = CallHistory::with(['lead' => function($query){
                    $query->select([
                        'id',
                        'country_code',
                        'phone',
                        'name',
                        'last_call',
                        'next_follow_up',
                        'lead_status',
                        'assigned_to',
                    ]);
                }])
                ->where(config('access.call_history_table').'.called_by', $user->id)
                ->where(config('access.call_history_table').'.saved', '1');
        if($medium != null && $medium != '')
        {
            $query->whereHas('lead', function($q) use($medium){
                $q->where('data_medium', $medium);
            });
        }
        if($city != null && $city != '')
        {
            $query->whereHas('lead', function($q) use($city){
                $q->where('city', $city);
            });
        }

        // if($search['value'] == null || $search['value'] == '')
        // {
            if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
            {
                $query->whereDate(config('access.call_history_table').'.created_at', '>=', $startDate);
                $query->whereDate(config('access.call_history_table').'.created_at', '<=', $endDate);
            }
        // }

        if($leadStatus != '')
        {
            $query->where( config('access.call_history_table').'.lead_status', $leadStatus);    
        }
        $query->whereIn(config('access.call_history_table').'.id', function($q){
            $q->select(DB::raw('MAX(id) FROM '.config('access.call_history_table').' WHERE saved="1" GROUP BY lead_id'));
        });
        $query->groupBy(config('access.call_history_table').'.lead_id');
        $query->orderBy(config('access.call_history_table').'.created_at', 'DESC');

        return $query;
    }

    function getCalledListForManager($request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $leadStatus = $request->get('lead_status');
        $executive = $request->get('executive');
        $medium = $request->get('medium');
        $city = $request->get('city');
        $search = $request->get('search');
        $frequency = $request->get('frequency');

        $query = DB::table('leads as l')
                        ->leftJoin('call_history', 'call_history.id', 'l.last_call_id')
                        ->leftJoin('users as u1', 'u1.id', 'call_history.called_by')
                        ->leftJoin('users as u2', 'u2.id', 'l.assigned_to')
                        ->select(['l.id as lead_id', 'l.data_user_id', 'l.country_code', 'l.phone', 'l.name', 'l.data_medium', 'l.last_call', 'l.next_follow_up', 'l.lead_status', 'l.assigned_to', 
                        'call_history.id', 'call_history.exotel_sid','call_history.duration', 'call_history.call_record_file', 'call_history.called_by', 'call_history.created_at',
                        'u1.name as called_by_name', 'u2.name as assigned_to_name', 'l.frequency']);
        // $query = DB::table('call_history')
        //                 ->leftJoin('leads as l', 'l.id', 'call_history.lead_id')
        //                 ->leftJoin('users as u1', 'u1.id', 'call_history.called_by')
        //                 ->leftJoin('users as u2', 'u2.id', 'l.assigned_to')
        //                 ->join(DB::raw('(Select max(id) as id from '.config('access.call_history_table').' group by lead_id) LastCallRow'), function($join) {
        //                     $join->on('call_history.id', '=', 'LastCallRow.id');
        //                 })
        //                 ->select(['l.id as lead_id', 'l.data_user_id', 'l.phone', 'l.name', 'l.data_medium', 'l.last_call', 'l.next_follow_up', 'l.lead_status', 'l.assigned_to', 
        //                 'call_history.id', 'call_history.exotel_sid','call_history.duration', 'call_history.call_record_file', 'call_history.called_by', 'call_history.created_at',
        //                 'u1.name as called_by_name', 'u2.name as assigned_to_name', DB::raw('count(call_history.lead_id) as frequency')]);

        if($frequency != null && $frequency != '')
        {
            $query->where('l.frequency', $frequency);
        }
        if($medium != null && $medium != '')
        {
            $query->where('l.data_medium', $medium);
        }

        if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
        {
            $query->whereDate(config('access.call_history_table').'.created_at', '>=', $startDate);
            $query->whereDate(config('access.call_history_table').'.created_at', '<=', $endDate);
        }

        $query->where(config('access.call_history_table').'.saved', '1');

        if($leadStatus != '')
        {
            $query->where( config('access.call_history_table').'.lead_status', $leadStatus);    
        }
        if($executive != '' && $executive != NULL)
        {
            $query->where(config('access.call_history_table').'.called_by', $executive);    
        }
        // $query->whereIn(config('access.call_history_table').'.id', function($q){
        //     $q->select(DB::raw('MAX(id) FROM '.config('access.call_history_table').' GROUP BY lead_id'));
        // });
        // $query->groupBy(config('access.call_history_table').'.lead_id');
        $query->orderBy(config('access.call_history_table').'.created_at','DESC');

        return $query;
    }

    function getCalledListForManagerCount($request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $leadStatus = $request->get('lead_status');
        $executive = $request->get('executive');
        $medium = $request->get('medium');
        $city = $request->get('city');
        $search = $request->get('search');
        $frequency = $request->get('frequency');

        $query = DB::table('leads as l')
                        ->leftJoin('call_history', 'call_history.id', 'l.last_call_id')
                        ->select(['l.id', 'l.frequency']);
        // $query = DB::table('call_history')
        //                 ->leftJoin('leads as l', 'l.id', 'call_history.lead_id')
        //                 ->select(['call_history.id']);
        if($frequency != null && $frequency != '')
        {
            $query->where('l.frequency', $frequency);
        }
        if($medium != null && $medium != '')
        {
            $query->where('l.data_medium', $medium);
        }

        if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
        {
            $query->whereDate(config('access.call_history_table').'.created_at', '>=', $startDate);
            $query->whereDate(config('access.call_history_table').'.created_at', '<=', $endDate);
        }

        $query->where(config('access.call_history_table').'.saved', '1');

        if($leadStatus != '')
        {
            $query->where(config('access.call_history_table').'.lead_status', $leadStatus);    
        }
        if($executive != '' && $executive != NULL)
        {
            $query->where(config('access.call_history_table').'.called_by', $executive);    
        }
        // $query->whereIn(config('access.call_history_table').'.id', function($q){
        //     $q->select(DB::raw('MAX(id) FROM '.config('access.call_history_table').' GROUP BY lead_id'));
        // });
        // $query->groupBy(config('access.call_history_table').'.lead_id');
        $query->orderBy(config('access.call_history_table').'.created_at','DESC');

        return $query->get()->count();
    }

    function leadSearch($request)
    {
        $searchTerm = $request->get('searchTerm');

        $query = Lead::where('phone', $searchTerm)
                        ->orWhereHas('alternateNumbers', function($query) use($searchTerm){
                            $query->where('phone', $searchTerm);
                        });

        return $query;
    }

    function getPrimaryNumber($lead)
    {
        if($lead->preferred == '1')
        {
            return $lead->phone;
        }else{
           return $lead->alternateNumbers()->where('preferred', '0')->value('phone');
        }
    }

    function executiveDashboardStats($user, $startDate, $endDate, $from="web")
    {
        $return = array();
        $return['totalCall']            = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->count();
        $return['totalUniqueCall']      = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->groupBy('phone')
                                            ->select('phone')
                                            ->get()
                                            ->count();
        $return['newCall']              = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('type', 'new')
                                            ->count();
        $return['repeatCall']           = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('type', 'repeat')
                                            ->count();
        $return['noAudioCalls']           = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->whereNull('duration')
                                            ->count();
        if($from == 'web'){
            $return['recentCalls']          = CallHistory::where('called_by', $user->id)
                                                ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                                ->where('saved', '1')
                                                ->orderBy('created_at', 'DESC')
                                                ->limit(5)
                                                ->get();
        }

        $return['totalSaleLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'sale')
                                            ->count();
        $return['totalHotLeads']        = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'hot')
                                            ->count();
        $return['totalMildLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'mild')
                                            ->count();
        $return['totalColdLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'cold')
                                            ->count();
        $return['totalNoAnswerLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'no_answer')
                                            ->count();
        $return['totalBusyLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'busy')
                                            ->count();
        $return['totalNotInterestedLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'not_interested')
                                            ->count();
        $return['totalDeadLeads']       = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('lead_status', 'dead')
                                            ->count();
        
        $return['totalTrainingCall']    = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('call_type', 'training')
                                            ->count();
        $return['totalSaleCall']        = CallHistory::where('called_by', $user->id)
                                            ->whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                            ->where('saved', '1')
                                            ->where('call_type', 'sale')
                                            ->count();

        return $return;
    }

    function managerDashboardStats($startDate, $endDate)
    {
        $return = array();
        $return['totalWorkforce']   = DailyTrack::whereRaw(DB::raw('DATE(`in`) >= "'.$startDate.'" AND DATE(`in`) <= "'.$endDate.'"'))
                                        ->groupBy('user_id')
                                        ->select('user_id')
                                        ->get()
                                        ->count();
        $totalCallTime              = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->sum('duration');
        $return['totalCallTime']    = round(($totalCallTime/1000)/60); 

        $return['totalCall']        = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->count();
        $return['totalUniqueCall']  = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->groupBy('phone')
                                        ->select('phone')
                                        ->get()
                                        ->count();
        $return['newCall']          = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('type', 'new')
                                        ->where('saved', '1')
                                        ->count();
        $return['repeatCall']       = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('type', 'repeat')
                                        ->where('saved', '1')
                                        ->count();
        $return['noAudioCalls']       = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->whereNull('duration')
                                        ->where('saved', '1')
                                        ->count();
        $return['recentCalls']      = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->orderBy('created_at', 'DESC')
                                        ->limit(5)
                                        ->get();

        $return['totalSaleLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'sale')
                                        ->count();
        $return['totalHotLeads']    = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'hot')
                                        ->count();
        $return['totalMildLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'mild')
                                        ->count();
        $return['totalColdLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'cold')
                                        ->count();
        $return['totalNoAnswerLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'no_answer')
                                        ->count();
        $return['totalBusyLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'busy')
                                        ->count();
        $return['totalNotInterestedLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'not_interested')
                                        ->count();
        $return['totalDeadLeads']   = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('lead_status', 'dead')
                                        ->count();
        
        $return['totalTrainingCall'] = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('call_type', 'training')
                                        ->count();
        $return['totalSaleCall']    = CallHistory::whereRaw(DB::raw('DATE(created_at) >= "'.$startDate.'" AND DATE(created_at) <= "'.$endDate.'"'))
                                        ->where('saved', '1')
                                        ->where('call_type', 'sale')
                                        ->count();

        return $return;
    }

    function leadChangeNumber($newNumber, $oldNumber, $newName, $newRelation)
    {
        $newNumber = str_replace('-', '', $newNumber);

        //check in leads
        $checkLead = $this->query()->where('phone', $newNumber)->count();
        if($checkLead > 0)
        {
            return false;
        }

        //Lead
        $lead = $this->query()->where('phone', $oldNumber)->first();
        $oldName = $lead->name;
        $oldRelation = $lead->relation;

        //update in data bank
        $dataUser = DataUser::where('phone', $newNumber)->first();
        if($dataUser != null)
        {
            DataChild::where('data_user_id', $dataUser->id)->update(['data_user_id' => $lead->data_user_id]);

            $dataUser->moved_to_lead = '1';
            $dataUser->update();
        }

        //get user detail from login server
        $user = DB::connection('login')
                    ->table(config('table.login.users'))
                    ->where('country_code', $lead->country_code)
                    ->where('phone', $newNumber)
                    ->first();

        $lead->phone             = $newNumber;
        if($user != null)
        {
            $lead->name              = $user->name;
            $lead->messenger         = $user->messenger;
            $lead->messenger_id      = $user->parent_id;
            $lead->learning          = $user->learning;
            $lead->learning_id       = $user->learning_id;
            $lead->community         = $user->community;
            $lead->community_id      = $user->community_id;
            $lead->login_id          = $user->id;
            $lead->status            = $user->status;
            $lead->lat_long          = $user->lat_long;
            $lead->locality          = $user->locality;
            $lead->city              = $user->city;
            $lead->state             = $user->state;
            $lead->country           = $user->country;
            $lead->email_verified    = $user->email_verified;
            $lead->last_activity     = $user->last_activity;
            $lead->registered_on     = $user->created_at;
        }else{
            $lead->name              = '';
            $lead->messenger         = 0;
            $lead->messenger_id      = 0;
            $lead->learning          = 0;
            $lead->learning_id       = 0;
            $lead->community         = 0;
            $lead->community_id      = 0;
            $lead->login_id          = 0;
            $lead->status            = '';
            $lead->lat_long          = '';
            $lead->locality          = '';
            $lead->city              = '';
            $lead->state             = '';
            $lead->country           = '';
            $lead->email_verified    = '0';
            $lead->last_activity     = NULL;
            $lead->registered_on     = NULL;
        }
        $lead->name = $newName;
        $lead->relation = $newRelation;
        $lead->update();

        event(new LeadChangeNumber($lead, $oldNumber));

        //Remove if new number present in alternate number 
        AlternateNumber::where('lead_id', $lead->id)->where('phone', $newNumber)->delete();

        //check old number in alternate
        $alternateNumber = AlternateNumber::where('lead_id', $lead->id)->where('phone', $oldNumber)->first();
        if($alternateNumber == null)
        {
            $alternateNumber = new AlternateNumber;
            $alternateNumber->lead_id = $lead->id;
            $alternateNumber->phone = $oldNumber;
            $alternateNumber->name = $oldName;
            $alternateNumber->preferred = '0';
            $alternateNumber->relation = $oldRelation;
            $alternateNumber->save();
        }
        
        return true;
    }

    function getMediumFilter()
    {
        return $this->query()
            ->whereNotNull('data_medium')
            ->where('data_medium','!=' ,'')
            ->groupBy('data_medium')
            ->pluck('data_medium');
    }

    function getCitiesFilter()
    {
        return $this->query()
            ->whereNotNull('city')
            ->where('city','!=' ,'')
            ->groupBy('city')
            ->pluck('city');
    }

    function getCallListForAPI($request, $done='0')
    {
        $user       = $request->user();
        $dataMedium = $request->get('medium');
        $phase      = $request->get('phase');
        $type       = $request->get('type');
        $query      = $request->get('query');

        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->where('done', $done)
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
            ])
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'ASC');

        if($query != '' && $query != NULL)
        {
            $dataTableQuery->where('phone', 'like', $query.'%');
        }
        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('phase', $phase);
        }
        if($dataMedium != '' && $dataMedium != NULL)
        {
            $dataTableQuery->where('data_medium', $dataMedium);
        }
        if($type == 'new')
        {
            $dataTableQuery->whereNull('next_follow_up');
        }else if($type == 'followUp')
        {
            $dataTableQuery->whereNotNull('next_follow_up');
        }else if($type != '')
        {
            $dataTableQuery->where('lead_status', $type);
        }

        return $dataTableQuery->paginate(env('PER_PAGE'));
    }

    function searchLeadApi($request)
    {
        $user       = $request->user();
        $query      = $request->get('query');

        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
            ])
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'ASC');

        if($query != '' && $query != NULL)
        {
            $dataTableQuery->where('phone', 'like', $query.'%');
        }

        return $dataTableQuery->paginate(env('PER_PAGE'));
    }

    function getFollowUpListForAPI($request, $done='0')
    {
        $user           = $request->user();
        $dataMedium     = $request->get('medium');
        $phase          = $request->get('phase');
        $type           = $request->get('type');
        $query          = $request->get('query');

        $dataTableQuery = $this->query()
            ->where('assigned_to', $user->id)
            ->where('done', $done)
            ->select([
                config('access.leads_table').'.id',
                config('access.leads_table').'.country_code',
                config('access.leads_table').'.phone',
                config('access.leads_table').'.name',
                config('access.leads_table').'.city',
                config('access.leads_table').'.last_call',
                config('access.leads_table').'.next_follow_up',
                config('access.leads_table').'.data_medium',
                config('access.leads_table').'.subscription_type',
                config('access.leads_table').'.phase',
                config('access.leads_table').'.lead_status',
            ])
            ->orderBy('next_follow_up', 'ASC')
            ->orderBy('id', 'ASC');

        if($query != '' && $query != NULL)
        {
            $dataTableQuery->where('phone', 'like', $query.'%');
        }
        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('phase', $phase);
        }
        if($dataMedium != '' && $dataMedium != NULL)
        {
            $dataTableQuery->where('data_medium', $dataMedium);
        }
        $dataTableQuery->whereNotNull('next_follow_up');
        if($type != '')
        {
            $dataTableQuery->where('lead_status', $type);
        }

        return $dataTableQuery->paginate(env('PER_PAGE'));
    }

    function getCallHistoryForAPI($request)
    {
        $user           = $request->user();
        $startDate      = $request->get('start_date');
        $endDate        = $request->get('end_date');
        $medium         = $request->get('medium');
        $type           = $request->get('type');
        $query          = $request->get('query');

        $dataTableQuery = DB::table('leads as l')
                        ->leftJoin('call_history', 'call_history.id', 'l.last_call_id')
                        ->leftJoin('users as u1', 'u1.id', 'call_history.called_by')
                        ->leftJoin('users as u2', 'u2.id', 'l.assigned_to')
                        ->select(['l.id as lead_id', 'l.data_user_id', 'l.country_code','l.phone', 'l.name', 'l.data_medium', 'l.last_call', 'l.next_follow_up', 'l.lead_status', 'l.assigned_to', 
                        'call_history.id', 'call_history.exotel_sid','call_history.duration', 'call_history.call_record_file', 'call_history.called_by', 'call_history.created_at',
                        'u1.name as called_by_name', 'u2.name as assigned_to_name'])
                        ->where('call_history.called_by', $user->id);

        if($query != null && $query != '')
        {
            $dataTableQuery->where('l.phone', 'like', $query.'%');
        }
        if($medium != null && $medium != '')
        {
            $dataTableQuery->where('l.data_medium', $medium);
        }

        if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
        {
            $dataTableQuery->whereDate(config('access.call_history_table').'.created_at', '>=', $startDate);
            $dataTableQuery->whereDate(config('access.call_history_table').'.created_at', '<=', $endDate);
        }

        if($type != '')
        {
            $dataTableQuery->where( config('access.call_history_table').'.lead_status', $type);    
        }

        $dataTableQuery->orderBy(config('access.call_history_table').'.created_at', 'DESC');

        return $dataTableQuery->paginate(env('PER_PAGE'));
    }

    function addLeadNote($lead, $user, $request)
    {
        $leadNote = new LeadNote;
        $leadNote->lead_id = $lead->id;
        $leadNote->user_id = $user->id;
        $leadNote->note = $request['note'];
        $leadNote->save();

        //update lead
        $lead->last_activity = $leadNote->created_at->format('Y-m-d H:i:s');
        $lead->update();

        //trigger event
        event(new LeadNoteAdded($lead, $user, $leadNote));
    }
}