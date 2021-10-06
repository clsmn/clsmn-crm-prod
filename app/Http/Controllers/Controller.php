<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function generateLoginRequestId()
    {
        $requestID = substr(md5(rand().time()), 0, 20);
        DB::connection('login')
            ->table(config('table.login.crm_request'))
            ->insert([
                'request_id' => $requestID
            ]);
        
        return $requestID;
    }

    function removeLoginRequestId($requestID)
    {
        return DB::connection('login')
                ->table(config('table.login.crm_request'))
                ->where('request_id', $requestID)
                ->delete();
    }

    function generateLearningRequestId()
    {
        $requestID = substr(md5(rand().time()), 0, 20);
        DB::connection('learning')
            ->table(config('table.learning.crm_request'))
            ->insert([
                'request_id' => $requestID
            ]);
        
        return $requestID;
    }

    function removeLearningRequestId($requestID)
    {
        return DB::connection('learning')
                ->table(config('table.learning.crm_request'))
                ->where('request_id', $requestID)
                ->delete();
    }
}
