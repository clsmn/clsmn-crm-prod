<?php

namespace App\Repositories\Backend\DataUser;

use App\Models\Lead\Lead;
use App\Models\Data\DataUser;
use App\Models\Data\DataChild;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use App\Events\Backend\Lead\LeadAssigned;

/**
 * Class DataUserRepository.
 */
class DataUserRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = DataUser::class;

    function getForDataTable($request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $movedToLead = $request->get('moved_to_lead');
        $lastCallBefore = $request->get('last_call_before');
        $medium = $request->get('medium');
        $phase = $request->get('phase');
        $stage = $request->get('stage');
        $subscriptionType = $request->get('subscription_type');
        $city = $request->get('city');
        $searchTerm = $request->get('searchTerm');

        $dataTableQuery = DB::table('data_user as du')
                            ->leftJoin('users as u', 'u.id', '=', 'du.assigned_to');
        $dataTableQuery->select([
                'du.id',
                'du.country_code',
                'du.phone',
                'du.name',
                'du.messenger',
                'du.learning',
                'du.community',
                'du.city',
                'du.data_medium',
                'du.phase',
                'du.subscription_type',
                'du.moved_to_lead',
                'du.lead_status',
                'du.lead_next_follow_up',
                'du.lead_last_call',
                'du.updated_at',
                'u.name as user_name',
            ]);
        if($city != '' && $city != NULL)
        {
            $dataTableQuery->where('du.city', $city);
        }
        if($searchTerm != '' && $searchTerm != NULL)
        {
            $dataTableQuery->where('du.phone', 'like', '%'.$searchTerm.'%');
        }
        if($medium != '' && $medium != NULL)
        {
            $dataTableQuery->where('du.data_medium', $medium);
        }
        if($phase != '' && $phase != NULL)
        {
            $dataTableQuery->where('du.phase', $phase);
        }
        if($stage != '' && $stage != NULL)
        {
            $dataTableQuery->where('du.lead_status', $stage);
        }
        if($lastCallBefore != '' && $lastCallBefore != NULL)
        {
            $dataTableQuery->whereDate('du.lead_last_call', '<=', $lastCallBefore);
        }
        if($subscriptionType != 'all')
        {
            if($subscriptionType != 'NULL')
            {
                $dataTableQuery->where('du.subscription_type', $subscriptionType);
            }else
            {
                $dataTableQuery->whereNull('du.subscription_type');
            }
        }
        if($movedToLead != '' && $movedToLead != NULL)
        {
            $dataTableQuery->where('du.moved_to_lead', $movedToLead);
        }

        if($startDate != null && $startDate != '' && $endDate != null && $endDate != '')
        {
            $dataTableQuery->whereDate('du.created_at', '>=', $startDate);
            $dataTableQuery->whereDate('du.created_at', '<=', $endDate);
        }
        $dataTableQuery->orderBy('updated_at', 'DESC');
        return $dataTableQuery;
    }

    function getForSearchDataTable($request)
    {
        $register_date = $request->get('register_date');
        $school = $request->get('school');
        $city = $request->get('city');
        $leadStage = $request->get('lead_stage');
        $searchTerm = $request->get('searchTerm');

        $dataTableQuery = DB::table('data_user as du');
        if(($school != null && $school!= '') || ($register_date != null && $register_date != ''))
        {
            $dataTableQuery->leftJoin('data_child as dc', 'du.id', '=', 'dc.data_user_id');
            if($register_date != null && $register_date != '')
            {
                $dataTableQuery->whereDate('dc.added_on', '>=', $register_date);
            }
            if($school != null && $school!= '')
            {
                $dataTableQuery->where('dc.school_id', $school);
            }
            $dataTableQuery->groupBy('dc.data_user_id');
        }
        $dataTableQuery->where('du.moved_to_lead', '0')
            ->select([
                'du.id',
                'du.country_code',
                'du.phone',
                'du.name',
                'du.messenger',
                'du.learning',
                'du.community',
                'du.city',
                'du.data_medium',
            ]);
        if($city != '' && $city != NULL)
        {
            $dataTableQuery->where('du.city', $city);
        }
        if($searchTerm != '' && $searchTerm != NULL)
        {
            $dataTableQuery->where('du.phone', $searchTerm);
        }
        if($leadStage != '' && $leadStage != NULL)
        {
            $dataTableQuery->where('du.lead_stage', $leadStage);
        }
        //dd($dataTableQuery->toSql());

        return $dataTableQuery;
    }

    function getCitiesFilter()
    {
        return $this->query()
            ->whereNotNull('city')
            ->where('city','!=' ,'')
            ->groupBy('city')
            ->pluck('city');
    }

    function getMediumFilter()
    {
        return $this->query()
            ->whereNotNull('data_medium')
            ->where('data_medium','!=' ,'')
            ->groupBy('data_medium')
            ->orderBy('data_medium')
            ->pluck('data_medium');
    }

    function getSchoolsFilter()
    {
        $schoolArr = DataChild::whereNotNull('school_id')
                        ->where('school_id', '!=', '0')
                        ->select('school_id', 'school_name', 'school_branch')
                        ->whereNull('deleted_at')
                        ->get();
        $schools = array();
        foreach($schoolArr as $school)
        {
            $schools[$school->school_id] = $school->school_name.', '.$school->school_branch. '('.$school->school_id.')';
        }
        return $schools;
    }

    function assignLeadToExecutive($request)
    {
        $dataUserId = $request->get('dataUserId');
        $executive = $request->get('executive');
        $callDate = $request->get('callDate');
        $assignToMass = $request->get('assignToMass');
        $ids = $request->get('id');

        $arr = explode('/', $callDate);
        $callDate = $arr[2].'-'.$arr[1].'-'.$arr[0];

        $user = User::findOrFail($executive);

        DB::transaction(function () use ($user, $dataUserId, $callDate, $assignToMass, $ids) {
            if($assignToMass == 'true')
            {
                $data = DataUser::whereIn('id', $ids)->get();    
            }else{
                $data = DataUser::where('id', $dataUserId)->get();
            }

            if($data->count() > 0)
            {
                //update moved_to_lead in data pool.
                //$dataIds = $data->pluck('id');
                //DataUser::whereIn('id', $dataIds)->update(['moved_to_lead' => '1', 'assigned_to' => $user->id]);

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
                        
                        history()->withType('Lead')
                                ->withSubType('assigned')
                                ->withEntity($lead->id)
                                ->withText('trans("history.backend.lead.transferred")')
                                ->withIcon('plus')
                                ->withClass('bg-green')
                                ->withAssets([
                                    'user_string' => $user->name,
                                    'olduser_string' => ($oldUser)? $oldUser->name:'',
                                    'date_string' => date('d M y'),
                                ])
                                ->log();
                    }
                }

                return true;
            }
        });

    }

}