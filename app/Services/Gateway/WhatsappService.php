<?php

namespace App\Services\Gateway;

use App\Models\Whatsapp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService {

    protected $base_url = "https://gateway.yakuzahost.com";

    public function processSingleChat($chat, $secret)
    {
        //send to gateway
        $chat['secret'] = $secret;
        $end_point = "/api/send/whatsapp";
        Log::info("Request Send WA Single : ". json_encode($chat));
        // $response = Http::post($this->base_url.$end_point, $chat);
        // $resp = json_decode($response);

        $cURL = curl_init($this->base_url.$end_point);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);
        $response = curl_exec($cURL);
        curl_close($cURL);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            $resp->status = true;
            Log::info("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }else{
            $resp->status = false;
            Log::error("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }

    public function processBulkChat($chat, $secret)
    {
        $chat['secret'] = $secret;

        $end_point = "/api/send/whatsapp.bulk";
        // $response = Http::post($this->base_url.$end_point, $chat);
        // $resp = json_decode($response);

        Log::info("Request Send WA Bulk : ". json_encode($chat));
        // $response = Http::post($this->base_url.$end_point, $chat);
        // $resp = json_decode($response);

        $cURL = curl_init($this->base_url.$end_point);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);
        $response = curl_exec($cURL);
        curl_close($cURL);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            $resp->status = true;
            Log::info("Response Send WA Bulk ".$this->base_url.$end_point." : ". $response);
        }else{
            $resp->status = false;
            Log::error("Response Send WA Bulk ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }

    public function getAccounts($secret, $limit=100, $page=1)
    {   
        $end_point = "/api/get/wa.accounts";
        $response = Http::get($this->base_url.$end_point, [
            'secret' => $secret,
            'limit' => $limit,
            'page' => $page,
        ]);

        $resp = json_decode($response, true);
        
        if($resp['status'] = 200){
            Log::info("Response Get WA Accounts ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Get WA Accounts ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }

    public function initializeBulkChat($request, $account, $recipients){
        
        $bulk = [
            "account" => $account,
            "campaign" => !empty($request->campaign)?$request->campaign:"Bulk Whatsapp",
            "recipients" => $recipients,
            // "groups" => "",
            "type" => !empty($request->type)?$request->type:"text",
            "message" => !empty($request->message)?$request->message:"",
            // "media_file" => !empty($request->media_file)?$request->media_file:"",
            // "media_url" => !empty($request->media_url)?$request->media_url:"",
            // "media_type" => !empty($request->media_type)?$request->media_type:"",
            // "document_file" => !empty($request->document_file)?$request->document_file:"",
            // "document_url" => !empty($request->document_url)?$request->document_url:"",
            // "document_type" => !empty($request->document_type)?$request->document_type:"",
            // "button_1" => !empty($request->button_1)?$request->button_1:"",
            // "button_2" => !empty($request->button_2)?$request->button_2:"",
            // "button_3" => !empty($request->button_3)?$request->button_3:"",
            // "list_title" => !empty($request->list_title)?$request->list_title:"",
            // "menu_title" => !empty($request->menu_title)?$request->menu_title:"",
            // "footer" => !empty($request->footer)?$request->footer:"",
            // "format" => !empty($request->format)?$request->format:"",
            // "shortener" => !empty($request->shortener)?$request->shortener:"",
        ];

        return $bulk;
    }

    public function initializeSingleChat($request, $account, $recipient)
    {
        $chat = [
            "account" => $account,
            "recipient" => $recipient,
            "type" => !empty($request->type)?$request->type:"text",
            "message" => !empty($request->message)?$request->message:"",
            // "media_file" => !empty($request->media_file)?$request->media_file:"",
            // "media_url" => !empty($request->media_url)?$request->media_url:"",
            // "media_type" => !empty($request->media_type)?$request->media_type:"",
            // "document_file" => !empty($request->document_file)?$request->document_file:"",
            // "document_url" => !empty($request->document_url)?$request->document_url:"",
            // "document_type" => !empty($request->document_type)?$request->document_type:"",
            // "button_1" => !empty($request->button_1)?$request->button_1:"",
            // "button_2" => !empty($request->button_2)?$request->button_2:"",
            // "button_3" => !empty($request->button_3)?$request->button_3:"",
            // "list_title" => !empty($request->list_title)?$request->list_title:"",
            // "menu_title" => !empty($request->menu_title)?$request->menu_title:"",
            // "footer" => !empty($request->footer)?$request->footer:"",
            // "format" => !empty($request->format)?$request->format:"",
            // "shortener" => !empty($request->shortener)?$request->shortener:"",
        ];

        return $chat;
    }

    public function initializeData($message, $device_id, $recipient)
    {
        $data = new Whatsapp();
        $data->account = $device_id;
        $data->recipient = $recipient;
        $data->type = "text";
        $data->message = $message;
        return $data;
    }

    public function send($data, $secret)
    {
        //send to gateway
        $chat = [
            "account" => $data->account,
            "recipient" => $data->recipient,
            "type" => $data->type,
            "message" => $data->message,
            "secret" => $secret
        ];
        
        $end_point = "/api/send/whatsapp";
        Log::info("Request Send WA Single : ". json_encode($chat));
        // $response = Http::post($this->base_url.$end_point, $chat);
        // $resp = json_decode($response);

        $cURL = curl_init($this->base_url.$end_point);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);
        $response = curl_exec($cURL);
        curl_close($cURL);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            $resp->status = true;
            Log::info("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }else{
            $resp->status = false;
            Log::error("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }


}
