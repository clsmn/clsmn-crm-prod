<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\Access\User\UserRepository;

class AuthController extends Controller
{
    public $userRepository = '';

    public function __construct(UserRepository $userRepository)
    {
       $this->userRepository = $userRepository;
    }

    /**
     * Check user and return hasPassword show app can display password field.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data   =   $request->all();
        $validator = Validator::make($data, [
            'email'  => 'required',
            'password'         => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['Message' => 'Validation Failed', 'Status' => '422', 'Data' => [] ])
                    ->setStatusCode(422);
        }else
        {
            $user = $this->userRepository->getLoginUser($data);
            $data['HasPassword'] = "0";
            $response = array();
            if($user)
            {
                $resData = array(
                    'user' => $user,
                    'token' => $user->createToken('OauthToken')->accessToken
                );
                $response['Status']    =   200;
                $response['Message']   =   "Success";
                $response['Data']      =   $resData;
                return response()->json($response)->setStatusCode(200);
            }

            $response['Status']    =   405;
            $response['Message']   =   "User not found";
            $response['Data']      =   array();
            return response()->json($response)->setStatusCode(200);
        }
    }
}
