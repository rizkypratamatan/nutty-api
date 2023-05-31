<?php

namespace App\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService {

    protected $base_url = "https://gateway.yakuzahost.com";
    protected $secret = "6dab02e46a9294ffb90fde8bcb76de411ad56d6d";

    public function processSingleChat($message)
    {
        //send to gateway
        $message['secret'] = $this->secret;
        $end_point = "/api/send/sms";
        $response = Http::post($this->base_url.$end_point, $message);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            $resp->status = true;
            Log::info("Response Send Message Single ".$this->base_url.$end_point." : ". $response);
        }else{
            $resp->status = false;
            Log::error("Response Send Message Single ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }

    public function processBulkChat($message)
    {
        $message['secret'] = $this->secret;

        $end_point = "/api/send/sms.bulk";
        $response = Http::post($this->base_url.$end_point, $message);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            Log::info("Response Send Message Bulk ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Send Message Bulk ".$this->base_url.$end_point." : ". $response);
        }
        return $resp;
    }

    public function getDevices($limit=100, $page=1)
    {   
        $end_point = "/api/get/devices";
        $response = Http::get($this->base_url.$end_point, [
            'secret' => $this->secret,
            'limit' => $limit,
            'page' => $page,
        ]);

        $resp = json_decode($response, true);
        
        if($resp['status'] = 200){
            Log::info("Response Get Devices ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Get Devices ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;

        // response example
        // {
        //     "status": 200,
        //     "message": "Android Devices",
        //     "data": [
        //          {
        //              "id": "49",
        //              "unique": "00000000-0000-0000-d57d-f30cb6a89289",
        //              "name": "F11 Phone",
        //              "version": "Android 11",
        //              "manufacturer": "OPPO",
        //              "random_send": false,
        //              "random_min": 5,
        //              "random_max": 10,
        //              "limit_status": true,
        //              "limit_interval": "daily",
        //              "limit_number": 100,
        //              "notification_packages": [
        //                  "com.google.android.apps.messaging",
        //                  "com.facebook.orca"
        //              ],
        //              "partner": false,
        //              "partner_sim": [
        //                  "2"
        //              ],
        //              "partner_priority": false,
        //              "partner_country": "PH",
        //              "partner_rate": 5,
        //              "partner_currency": "PHP",
        //              "created": 1636462504
        //          }
        //      ]
        //   }
    }

    public function initializeBulkChat($request, $device, $numbers){
        $chat = [
            "mode" => "devices",
            "campaign" => !empty($request->campaign)?$request->campaign:"",
            "numbers" => $numbers,
            "groups" => !empty($request->groups)?$request->groups:"",
            "message" => !empty($request->message)?$request->message:"",
            "device" => $device,
            // "gateway" => "",
            "sim" => "",
            "priority" => "",
            // "shortener" => "",
        ];

        return $chat;
    }

    public function initializeSingleData($message, $device, $phone){
        $chat = [
            "mode" => "devices",
            "phone" => $phone,
            "message" => $message,
            "device" => $device,
            "sim" => 1,
            "priority" => 1,
            "shortener" => "",
        ];

        return $chat;


    }


}
