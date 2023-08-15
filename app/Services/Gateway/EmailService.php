<?php

namespace App\Services\Gateway;

use App\Mail\EmailBroadcast;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService {

    public function sendEmail($recipient, $data, $setting)
    {
        // 'mailgun' => [
        //     'domain' => env('MAILGUN_DOMAIN'),
        //     'secret' => env('MAILGUN_SECRET'),
        //     'endpoint' => env('MAILGUN_ENDPOINT'),
        //     'scheme' => 'https',
        // ],

        $config = [
                'domain' => $setting->mailgun_domain,
                'secret' => $setting->mailgun_domain,
                'endpoint' => env('MAILGUN_ENDPOINT'),
                'scheme' => 'https',
        ];
    
        Config::set('mailgun', $config);

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

    public function initializeDataEmail($subject, $message, $email, $from_name = "Nutty CRM", $from_email = "system@nutty.com", $attc="", $initiated_time="", $schedule_status="now"){
        $data = new Email();
        $data->from_name = $from_name;
        $data->from_email = $from_email;
        $data->to_email = $email;
        $data->subject = $subject;
        $data->message = $message;
        $data->attachment = $attc;
        $data->status = "processed";
        $data->initiated_time = ($initiated_time)?$initiated_time:date("Y-m-d H:i:s");
        $data->schedule_status = $schedule_status;

        return $data;
    }

}
