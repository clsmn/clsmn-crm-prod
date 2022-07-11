<?php

namespace App\Repositories\Backend\Facebook;

use App\Models\Facebook\FacebookLeads;
use App\Models\Facebook\FacebookCmpAds;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use App\Models\Data\DataUser;
use Ixudra\Curl\Facades\Curl;
/**
 * Class LeadRepository.
 */
class FacebookLeadsRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = FacebookLeads::class;

    function getCampaigns($s)
    {
        if($s == 'all')
        {
           $campaigns = FacebookLeads::paginate(250);
        }
        else if($s != 'all' && ($s != '' || $s != null))
        {
           $campaigns = FacebookLeads::where('status',$s)->paginate(250);
        }
        else
        {
           $campaigns = FacebookLeads::paginate(250);
        }
       return $campaigns;
    }
    function getCampaignsStatus()
    {
       $status = FacebookLeads::select('status')->groupBy('status')->get();
       return $status;
    }
    
    function updateCompaign($request)
    {
        FacebookLeads::where('id', $request->id)->update(['campaign_source_name' => $request->value]);
        $data = DB::table('facebook_leads')
            ->select('facebook_leads.campaign_source_name','facebook_leads.campaign_id','facebook_campaign_ads.ad_id')
            ->join('facebook_campaign_ads', 'facebook_campaign_ads.campaign_id', '=', 'facebook_leads.campaign_id')
            ->where('facebook_leads.campaign_id', $request->cmp_id)
            ->get();
        if(count($data) > 0)
        {
            foreach($data as $value)
            {
                DB::table('data_user')->where('data_medium', $value->ad_id)->update(array('data_medium' => $value->campaign_source_name));
                DB::table('leads')->where('data_medium', $value->ad_id)->update(array('data_medium' => $value->campaign_source_name));
                DB::table('call_history')->where('data_medium', $value->ad_id)->update(array('data_medium' => $value->campaign_source_name));
            }
        }
         return true;
    }

    function refreshCompaign()
    {
        $accounts = DB::table('facebook_ads_acc')->where('status',0)->get();
        // $accounts = DB::table('facebook_ads_acc')->where('status',0)->get();
        foreach($accounts as $acc)
        {
            $url = 'https://graph.facebook.com/v13.0/'.$acc->act_id.'/campaigns?fields=name,status,ads.limit(1000)&limit=1000';
            $dataCampaign = $this->curl_call($url);

            $dataFB = json_decode($dataCampaign);
            if(isset($dataFB->data))
            {
                $i = 1;
                foreach($dataFB->data as $value)
                {
                    try {
                        $count = FacebookLeads::where('campaign_id',$value->id)->count();
                        if($count == 0)
                        {
                            $cmp_id = $value->id;
                            $campaign = new FacebookLeads;
                            $campaign->campaign_id = $value->id;
                            $campaign->name = $value->name;
                            $campaign->status = $value->status;
                            if($campaign->save())
                            {
                                if(isset($value->ads->data))
                                {
                                    foreach($value->ads->data as $ads)
                                    {
                                        try {
                                            $count = FacebookCmpAds::where('ad_id',$ads->id)->count();
                                            if($count == 0)
                                            {
                                                $ad = new FacebookCmpAds;
                                                $ad->ad_id = $ads->id;
                                                $ad->campaign_id = $cmp_id;
                                                $ad->save();
                                            }
                                        } 
                                        catch (Exception $e) 
                                        {
                                            print_r($e);
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            $campaign = FacebookLeads::select('name')->where('campaign_id',$value->id)->first();
                            if($campaign->name != $value->name)
                            {
                                FacebookLeads::where('campaign_id',$value->id)->update(['name'=>$value->name, 'status'=>$value->status]);
                            }

                        }
                    } 
                    catch (Exception $e) 
                    {
                        print_r($e);
                    }
                }
            }

        }
        
      return true;
    }
    function curl_call($url)
    {
        $token = "EAAc7Qlwj1LcBACJ1ahkiMLrmUczu5xZAB0lRBDPwgmo1h5OfNCGKCZBIjMw4WQz3HpCknPLOeaBeOBh4JbuJVRiH3GKCZCOEnvBb2YBqBlTdT30X1deOpPtyZCc7laNtIeOuJ1PZBxZALB8gAgdUEkfNeJuOaJ1gNyVQkvlFeYbL30tRMH76Vq";
        $ch = curl_init($url);

            // Returns the data/output as a string instead of raw data
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //Set your auth headers
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
               'Content-Type: application/json',
               'Authorization: Bearer ' . $token
               ));
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
    }
    // function refreshAds()
    // {
    //     $campaigns = FacebookLeads::select('campaign_id')->get();
    //     // dd($campaigns);
    //     foreach($campaigns as $campaign)
    //     {
    //         $url = 'https://graph.facebook.com/v13.0/'.$campaign->campaign_id.'/ads?fields=name,status&limit=500';
    //         $dataAds = $this->curl_call($url);
    //         $dataFBads = json_decode($dataAds);
    //         if(isset($dataFBads->data))
    //         {
    //             foreach($dataFBads->data as $ads)
    //             {
    //                 try {
    //                     $count = FacebookCmpAds::where('ad_id',$ads->id)->count();
    //                     if($count == 0)
    //                     {
    //                         $ad = new FacebookCmpAds;
    //                         $ad->ad_id = $ads->id;
    //                         $ad->campaign_id = $campaign->campaign_id;
    //                         $ad->name = $ads->name;
    //                         $ad->status = $ads->status;
    //                         $ad->save();
    //                     }
    //                 } 
    //                 catch (Exception $e) 
    //                 {
    //                     print_r($e);
    //                 }
    //             }
    //         }
    //     }
    // }
    function addDataUser($input)
    {

        
        foreach($input['entry'] as $leadgen)
        {
            foreach($leadgen['changes'] as $value)
            {
                if($value['field'] == 'leadgen')
                {
                    $medium = $value['value']['ad_id'];
                    $leadgen_id = $value['value']['leadgen_id'];
                    $token = "EAAc7Qlwj1LcBACJ1ahkiMLrmUczu5xZAB0lRBDPwgmo1h5OfNCGKCZBIjMw4WQz3HpCknPLOeaBeOBh4JbuJVRiH3GKCZCOEnvBb2YBqBlTdT30X1deOpPtyZCc7laNtIeOuJ1PZBxZALB8gAgdUEkfNeJuOaJ1gNyVQkvlFeYbL30tRMH76Vq";

                    $ch = curl_init('https://graph.facebook.com/v13.0/'.$leadgen_id.'/');

                    // Returns the data/output as a string instead of raw data
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    //Set your auth headers
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Content-Type: application/json',
                       'Authorization: Bearer ' . $token
                       ));
                    $data = curl_exec($ch);
                    curl_close($ch);
                    $webhookURL = 'https://chat.googleapis.com/v1/spaces/AAAAl0HmcSU/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8Awdlmcm58vWp2ZPcz1HPjuxypS1WVvtNigQVqTY1yM%3D';
                    Curl::to($webhookURL)
                        ->withData( array( 'text' => $data ) )
                        ->asJson()
                        ->post();
                    error_log(print_r($input, true));                          
                    $dataFBLead = json_decode($data);
                    foreach($dataFBLead->field_data as $lead)
                    {
                        if($lead->name == 'full_name')
                        {
                            $name = $lead->values[0];
                        }
                        if($lead->name == 'email')
                        {
                            $email = $lead->values[0];
                        }
                        if($lead->name == 'city')
                        {
                            $city = $lead->values[0];
                        }
                        if($lead->name == 'your_child_kit_category')
                        {
                            $message = $lead->values[0];
                        }
                        if($lead->name == 'phone_number')
                        {
                            $mobile = $lead->values[0];
                         if($mobile != '' && $mobile != null) 
                         {
                             try 
                             {
                                 $number = PhoneNumber::parse($mobile);
                                 $cc = $number->getCountryCode();
                                 $phoneNumber = $number->getNationalNumber();
                                 if($phoneNumber != '') {
                                     $mobile = $phoneNumber;
                                     $countryCode = '+'.$cc;    
                                 }else{
                                     $countryCode = '+91';    
                                     $rowData['phone'] = substr((int) $mobile, -10);
                                 }
                             }
                             catch (PhoneNumberParseException $e) 
                             {
                                 $countryCode = '+91';    
                                 $mobile = substr((int) $mobile, -10);
                             }

                         }
                        }
                     
                    }
                    
                    $data_medium = FacebookCmpAds::where('ad_id', $medium)->select('*')->first();

                    $dataUser = DataUser::where('country_code', $countryCode)->where('phone', $mobile)->first();
                    if(!$dataUser)
                    {
                        date_default_timezone_set('Asia/Kolkata');
                        $leadDate = date('Y-m-d H:i:s', 1649067251);

                        $dataUser = new DataUser;
                        $dataUser->name = $name;
                        $dataUser->country_code = $countryCode;
                        $dataUser->phone = $mobile;
                        $dataUser->status = '';
                        $dataUser->data_medium = ($data_medium->campaign->campaign_source_name)? $data_medium->campaign->campaign_source_name : $medium;
                        $dataUser->moved_to_lead = '0';
                        $dataUser->created_at = $leadDate;
                        $campaign_id = $data_medium->campaign->campaign_id;
                        FacebookLeads::where('campaign_id',$campaign_id)->increment('total');
                        FacebookLeads::where('campaign_id',$campaign_id)->increment('new');
                        return DB::transaction(function() use($dataUser, $input,$campaign_id){
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
                                
                                // return response()->json(['status' => '200', 'data' => 'Lead added.']);
                            }
                            // return response()->json(['status' => '422', 'data' => 'Some error occurred try again later.']);
                        });
                           
                    }
                    else
                    {
                        $dataUser->data_medium = ($data_medium->campaign->campaign_source_name)? $data_medium->campaign->campaign_source_name : $medium;
                        $dataUser->update();
                        FacebookLeads::where('campaign_id',$data_medium->campaign->campaign_id)->increment('total');
                        FacebookLeads::where('campaign_id',$data_medium->campaign->campaign_id)->increment('existing');
                        
                        // return response()->json(['status' => '200', 'data' => 'Lead updated.']);
                    }

                }
            }
        }
    }

}