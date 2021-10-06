<?php

namespace App\Http\Controllers\Backend\DataBank;

use Excel;
use App\Models\Lead\Lead;
use Illuminate\Http\Request;
use App\Models\Data\DataUser;
use Ixudra\Curl\Facades\Curl;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use App\Http\Requests\Backend\Lead\CreateLeadRequest;
use App\Repositories\Backend\DataUser\DataUserRepository;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

/**
 * Class DataBankController.
 */
class DataBankController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, DataUserRepository $dataBankRepo)
    {
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                            $query->orWhere('role_id', '2');
                        })
                        ->where('status', '1')
                        ->pluck('name', 'id');
        // $cities = $dataBankRepo->getCitiesFilter();
        $sources = $dataBankRepo->getMediumFilter();
        return view('backend.data_bank.index', compact('executives', 'sources'));
    }

    function moveToLead(Request $request, DataUserRepository $dataBankRepo)
    {
        $dataBankRepo->assignLeadToExecutive($request);
        return response()->json(['Message' => 'Success', 'Status' => '200']);
    }

    function create(Request $request)
    {
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                            $query->orWhere('role_id', '2');
                        })
                        ->where('status', '1');
        if($request->user()->hasRole('Executive'))
        {
            $executives->where('id', $request->user()->id);
        }
        $executives = $executives->pluck('name', 'id');
        
        if($request->user()->hasRole('Executive'))
        {
            return view('backend.data_bank.createExecutive', compact('executives'));
        }else{
            return view('backend.data_bank.create', compact('executives'));
        }
    }

    function store(CreateLeadRequest $request, DataUserRepository $dataBankRepo)
    {
        $medium = $request->get('medium');
        // insert into data bank
        $dataUser = new DataUser;
        $dataUser->name = $request->get('name');
        $dataUser->country_code = $request->get('country_code');
        $dataUser->phone = $request->get('phone');
        $dataUser->status = '';
        $dataUser->lat_long = $request->get('txtUserLatLng');
        $dataUser->locality = $request->get('txtUserLocality');
        $dataUser->city = $request->get('txtUserCity');
        $dataUser->state = $request->get('txtUserState');
        $dataUser->country = $request->get('txtUserCountry');
        $dataUser->data_medium = ($medium)? strtolower($medium) : 'manual';
        $dataUser->moved_to_lead = '0';

        DB::transaction(function() use($dataUser, $request,  $dataBankRepo){
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

                // assign lead
                if($request->get('assign') == '1')
                {
                    $executive = $request->get('assigned_to');
                    $callDate = $request->get('call_date');
                    $request->request->add([
                        'dataUserId' => $dataUser->id,
                        'executive'  => $executive,
                        'callDate'  => $callDate,
                    ]);
                    $dataBankRepo->assignLeadToExecutive($request);
                }

                return true;
            }

            throw new GeneralException('Some error occurred try again later.');
        });

        return redirect()->route('admin.data.bank.create')->withFlashSuccess('Lead added to system.');
    }

    function bulkCreate(Request $request, DataUserRepository $dataBankRepo)
    {
        $camp = $request->get('camp');
        $changeSource = $request->get('changeSource');
        if(!$camp)
        {
            $camp = 'Manual';
        }
        $data = $result = array();
        if($request->hasFile('import_file'))
        {
            $data = Excel::load($request->file('import_file')->getRealPath(), function ($reader) { })->get()->all();
        }
        
        if(count($data) > 0)
        {
            if(count($data) > 500)
            {
                $data = array_slice($data, 0, 500);
            }

            foreach($data as $row)
            {
                $rowData = $row->all();
                $rowData['country_code'] = ''; 
                if($rowData['phone'] != '' && $rowData['phone'] != null) {
                    try {
                        $number = PhoneNumber::parse($rowData['phone']);
                        $countryCode = $number->getCountryCode();
                        $phoneNumber = $number->getNationalNumber();
                        if($phoneNumber != '') {
                            $rowData['phone'] = $phoneNumber;
                            $rowData['country_code'] = '+'.$countryCode;    
                        }else{
                            $rowData['country_code'] = '+91';    
                            $rowData['phone'] = substr((int) $rowData['phone'], -10);
                        }
                    }
                    catch (PhoneNumberParseException $e) {
                        $rowData['country_code'] = '+91';    
                        $rowData['phone'] = substr((int) $rowData['phone'], -10);
                    }

                    $result[] = $rowData;
                }
                
                
            }

            $data = $result;
        }
        $sources = $dataBankRepo->getMediumFilter();
        $sources = $sources->toArray();
        $options = json_encode(['data' =>  $sources]);
        
        return view('backend.data_bank.bulk', compact('data', 'camp', 'changeSource','options'));
    }

    function bulkSave(Request $request, DataUserRepository $dataBankRepo)
    {
        $this->validate($request, [
            'phone' => 'required',
            'country_code' => 'required'
        ]);

        //Set default values
        $medium = $request->get('medium');
        $name = $request->get('name');
        $countryCode = $request->get('country_code');
        $phone = $request->get('phone');
        $city = $request->get('city');
        $adPlatform = $request->get('ad_platform');
        $changeSource = $request->get('change_source');

        if(!$medium)
        {
            $medium = 'manual';
        }
        if(!$name)
        {
            $name = 'User';
        }

        // Check in data bank
        $dataUser = DataUser::where('country_code', $countryCode)->where('phone', $phone)->first();
        if($dataUser)
        {
            $dataUser->ad_platform =  $dataUser->ad_platform;
            $dataUser->data_medium = ($changeSource == '1')? $medium: $dataUser->data_medium;
            $dataUser->updated_at = date('Y-m-d H:i:s');
            $dataUser->save();

            return response()->json(['status' => '200', 'data' => 'Lead added.']);
        }else{
            // insert into data bank
            $dataUser                   = new DataUser;
            $dataUser->name             = $name;
            $dataUser->country_code     = $countryCode;
            $dataUser->phone            = $phone;
            $dataUser->status           = '';
            $dataUser->city             = $city;
            $dataUser->ad_platform      = $adPlatform;
            $dataUser->data_medium      = $medium;
            $dataUser->moved_to_lead    = '0';

            return DB::transaction(function() use($dataUser, $request, $dataBankRepo){
                if($dataUser->save())
                {
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

                        // assign lead
                        $executive = $request->get('assigned_to');
                        $callDate = $request->get('call_date');
                        if($executive)
                        {
                            $request->request->add([
                                'dataUserId' => $dataUser->id,
                                'executive'  => $executive,
                                'callDate'  => $callDate,
                            ]);
                            $dataBankRepo->assignLeadToExecutive($request);
                        }
                    }
                    return response()->json(['status' => '200', 'data' => 'Lead added.']);
                }
                return response()->json(['status' => '422', 'data' => 'Some error occurred try again later.']);
            });
        }
    }

    function updateFromLead(Request $request)
    {
        // DB::beginTransaction();
        $dataUsers = DataUser::select(['id', 'phone', 'lead_status', 'lead_next_follow_up', 'lead_last_call', 'updated_at'])->where('is_updated', '0')->limit(10000)->get();
        foreach($dataUsers as $dataUser)
        {
            if($dataUser->lead)
            {
                $lead = $dataUser->lead;
                DataUser::where('id', $dataUser->id)
                        ->update([
                            'lead_status' => $lead->lead_status,
                            'lead_next_follow_up' => $lead->next_follow_up,
                            'lead_last_call' => $lead->last_call,
                            'updated_at' => $dataUser->updated_at,
                            'is_updated' => 1,
                        ]);
            }else{
                DataUser::where('id', $dataUser->id)
                        ->update([
                            'lead_status' => null,
                            'lead_next_follow_up' => null,
                            'lead_last_call' => null,
                            'updated_at' => $dataUser->updated_at,
                            'is_updated' => 1,
                        ]);
            }
        }
        // DB::commit();
        return 'All records updated!!!';
    }
}