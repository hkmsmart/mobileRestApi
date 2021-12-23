<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SubscriberStatu;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;
class Controller extends BaseController
{
    public $controlRequest = ARRAY();

    protected function createControl($request)
    {
        $deviceControl = Device::where('device_id', '=', $request->input('deviceId'))->first();
        if($deviceControl != null){
            return ARRAY ('result'=>1,'message'=>'');
        }
        else{
            return ARRAY ('result'=>0,'message'=>'');
        }
    }

    protected function createDeviceInfo($request)
    {

       $this->controlRequest = $request;
        $this->validateControl();
        if($this->controlRequest['result'] == 1){
            return ARRAY ('result'=>$this->controlRequest['result'],'message'=>$this->controlRequest['message']);
        }

        //User
        $email    = $request->input('email');
        $password = Hash::make($request->input('password'));

        $userId   = User::create([
            'email'    => $email,
            'password' => $password
        ])->id;

        //Device
        if($this->controlRequest['result'] == 1){
            return ARRAY ('result'=>$this->controlRequest['result'],'message'=>$this->controlRequest['message']);
        }

        $deviceId   = Device::create([
            'device_id' => $request->input('deviceId'),
            'app_id'    => $request->input('appId'),
            'language'  => $request->input('language'),
            'os'        => $request->input('os'),
            'uid'       => $userId
        ])->id;

        //subscriber_status
        if($this->controlRequest['result'] == 1){
            return ARRAY ('result'=>$this->controlRequest['result'],'message'=>$this->controlRequest['message']);
        }
        $deviceId   = SubscriberStatu::create([
            'uid'       => $userId,
            'device_id' => $deviceId,
            'statu'     => false
        ])->id;

        return ARRAY ('result'=>$this->controlRequest['result'],'message'=>$this->controlRequest['message'],'userId'=>$userId);
    }

    public function validateControl(){
        $validator = Validator::make($this->controlRequest->all(), [
            'email'    => 'required|unique:users,email',
            'password' => 'required|',
            'deviceId' => 'required|unique:devices,device_id',
            'appId'    => 'required|',
            'language' => 'required|',
            'os'       => 'required|'
        ]);

        $messages = $validator->errors();
        foreach ($messages->all() as $message) {
            $this->controlRequest['result']  = 1;
            $this->controlRequest['message'] .= $message;
        }
    }

    public function devicePurchase($user,$receipt){
        $SubscriberStatu = SubscriberStatu::where('uid', '=', $user->id)->get();

        if($SubscriberStatu[0]->id != ''){
            $date       = $this->carbonDate();
            $expireDate = $this->expireDate($date);
            SubscriberStatu::where('uid', '=', $user->id)->update(['statu' =>1,'start_date'=>$date,'receipt'=>$receipt]);
            return ARRAY ('result'=>1,'expireDate'=>$expireDate,'message'=>'');
        }
        else{
            return ARRAY ('result'=>0,'expireDate'=>'','message'=>'device not found');
        }
    }

    public function checkSubscribe($user){
        $SubscriberStatu = SubscriberStatu::where('uid', '=', $user->id)->get();

        if($SubscriberStatu[0]->id != ''){
            if($SubscriberStatu[0]->statu == 1){
                $expireDate = $this->expireDate($SubscriberStatu[0]->start_date);
                return ARRAY ('result'=>1,'expireDate'=>$expireDate,'message'=>'');
            }
            else{
                return ARRAY ('result'=>1,'expireDate'=>0,'message'=>'not subscribed');
            }

        }
        else{
            return ARRAY ('result'=>1,'expireDate'=>'','message'=>'device not found');
        }
    }

    public function carbonDate(){
        $datetime = Carbon::now();
        $datetime->setTimezone('America/Monterrey');
        return $datetime->format('Y-m-d h:i:s');
    }

    public function expireDate($date){
        $datetime    = Carbon::createFromFormat('Y-m-d h:i:s', $date);
        $newDateTime = $datetime->addYear();
        return $newDateTime->format('Y-m-d h:i:s');
    }
}
