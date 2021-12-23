<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
        //paramters control
        $controlResponse = $this->createControl($request);

        if($controlResponse['result'] == 1){
            return $this->login($request);
        }
        else{
            //creator
            $createResponse = $this->createDeviceInfo($request);
            if($createResponse['result'] == 1){
                return response()->json(['status' => 'fail','message'=>$createResponse['message']]);
            }
            else{
                return $this->login($request);
            }

        }
    }

    public function login(Request $request)
    {
        $email    = $request->input('email');
        $password = $request->input('password');

        if (! $token = auth()->attempt(['email'=>$email,'password'=>$password,]) ) {
            return response()->json(['status' => 'fail','message'=>'Unauthorized']);
        }
        else{
            return $this->respondWithToken($token);
        }
    }

    public function purchase(Request $request)
    {
        $receipt = $request->input('receipt');
        $user    = auth()->user();

        $devicePurchase = $this->devicePurchase($user,$receipt);

        if($devicePurchase['result'] == 1){
            return response()->json(['status' => 'success','expireDate'=>$devicePurchase['expireDate'],'message'=>'']);
        }
        else{
            return response()->json(['status' => 'fail','message'=>$devicePurchase['message']]);
        }
    }

    public function check()
    {
        $user = auth()->user();
        $devicePurchase = $this->checkSubscribe($user);
        if($devicePurchase['result'] == 1){
            return response()->json(['status' => 'success','expireDate'=>$devicePurchase['expireDate'],'message'=>'']);
        }
        else{
            return response()->json(['status' => 'fail','expireDate'=>'','message'=>$devicePurchase['message']]);
        }
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['status'=>'success','message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        $status = 'success';
        if($token == false){
            $status = 'fail';
        }

        return response()->json([
            'status'=>$status,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);

    }
}
