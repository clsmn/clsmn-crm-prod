<?php

namespace App\Http\Controllers\Backend\Facebook;

use Excel;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use App\Models\Data\DataUser;
use App\Models\Facebook\FacebookLeads;
use App\Repositories\Backend\Facebook\FacebookLeadsRepository;


class FacebookLeadsController extends Controller
{
    public function index(Request $request, FacebookLeadsRepository $FbLeadsRepo)
    {
        $s = $request->status;
        $campaigns = $FbLeadsRepo->getCampaigns($s);
        $status = $FbLeadsRepo->getCampaignsStatus();
        return view('backend.facebook.index', compact('campaigns','status','s'));
    }

    public function updateCompaignData(Request $request, FacebookLeadsRepository $FbLeadsRepo)
    {

        $updateCompaign = $FbLeadsRepo->updateCompaign($request);

    }

    public function refreshCompaignList(Request $request, FacebookLeadsRepository $FbLeadsRepo)
    {

        $refreshCompaign = $FbLeadsRepo->refreshCompaign();
        
    }
    
    public function refreshCompaignAds(Request $request, FacebookLeadsRepository $FbLeadsRepo)
    {

        $refreshCompaign = $FbLeadsRepo->refreshAds();
        
    }
    public function webhookLead( FacebookLeadsRepository $FbLeadsRepo)
    {
        if(isset($_REQUEST['hub_verify_token'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $verify_token = $_REQUEST['hub_verify_token'];

            if ($verify_token === 'abc123') {
                echo $challenge;
            }
        }
        $name = '';
        $countryCode = '';
        $mobile = '';
        $email = '';
        $message = '';
        $city = '';
        $input = json_decode(file_get_contents('php://input'), true);
        
        $addDataUser = $FbLeadsRepo->addDataUser($input);

        // $webhookURL = 'https://chat.googleapis.com/v1/spaces/AAAAl0HmcSU/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8Awdlmcm58vWp2ZPcz1HPjuxypS1WVvtNigQVqTY1yM%3D';
        // Curl::to($webhookURL)
        //     ->withData( array( 'text' => json_encode($input) ) )
        //     ->asJson()
        //     ->post();
        // // // $input = json_decode(file_get_contents('php://input'), true);
        // error_log(print_r($input, true));   

        // return http_response_code(200);
    }

    // public function updateSourceName(Request $request)
    // {
        
    // }
}
