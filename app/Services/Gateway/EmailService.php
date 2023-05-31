<?php

namespace App\Services\Gateway;

use App\Mail\EmailBroadcast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService {

    public function sendEmail($recipient, $data)
    {

        if($data['schedule_status'] == "now"){
            Mail::to($recipient)
            // ->cc($moreUsers)
            // ->bcc($evenMoreUsers)
            ->queue(new EmailBroadcast($data));
        }else{
            Mail::to($recipient)
            ->later(Carbon::parse($data['initiated_time']), new EmailBroadcast($data));
        }       
    }

    public function initializeData($request, $email){
        $chat = [
            "from_name" => !empty($request['from_name'])?$request['from_name']:"",
            "from_email" => !empty($request['from_email'])?$request['from_email']:"system@nutty.com",
            "to_email" => $email,
            "subject" => !empty($request['subject'])?$request['subject']:"",
            "message" => !empty($request['message'])?$request['message']:"",
            "attachment" => !empty($request['attachment'])?$request['attachment']:"",
            "status" => "queued",
            "initiated_time" => !empty($request['initiated_time'])?$request['initiated_time']:date("Y-m-d H:i:s"),
            "schedule_status" => !empty($request['schedule_status'])?$request['schedule_status']:"now" //now, later
        ];
        return $chat;
    }


}
