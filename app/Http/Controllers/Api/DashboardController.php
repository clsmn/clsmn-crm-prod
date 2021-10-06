<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Lead\LeadRepository;

class DashboardController extends Controller
{
    public $leadRepository = '';

    public function __construct(LeadRepository $leadRepository)
    {
       $this->leadRepository = $leadRepository;
    }

    function index(Request $request)
    {
        $user = $request->user();
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $startDate = ($startDate != NULL & $startDate != '')? $startDate : date('Y-m-d');
        $endDate = ($endDate != NULL & $endDate != '')? $endDate : date('Y-m-d');

        $data = $this->leadRepository->executiveDashboardStats($user, $startDate, $endDate, 'api');
        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }

    function appVersion() 
    {
        $data = array(
            'version' => '1.5'
        );
        return response()->json(['Message' => 'Success', 'Status' => '200', 'Data' => $data]);
    }
}