<?php

namespace App\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService {

    protected $base_url = "https://gateway.yakuzahost.com";
    protected $secret = "96e4b07ec13abd1327cfb3c34ec5ea23631fbc7f";

    public function processSingleChat($chat)
    {
        //send to gateway
        $chat['secret'] = $this->secret;
        $end_point = "/api/send/whatsapp";
        $response = Http::post($this->base_url.$end_point, $chat);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            Log::info("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Send WA Single ".$this->base_url.$end_point." : ". $response);
        }
    }

    public function processBulkChat($chat)
    {
        $chat['secret'] = $this->secret;

        $end_point = "/api/send/whatsapp.bulk";
        $response = Http::post($this->base_url.$end_point, $chat);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            Log::info("Response Send WA Bulk ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Send WA Bulk ".$this->base_url.$end_point." : ". $response);
        }

    }

    public function getAccounts($limit=10, $page=1)
    {   
        $end_point = "/api/get/wa.accounts";
        $response = Http::get($this->base_url.$end_point, [
            'secret' => $this->secret,
            'limit' => $limit,
            'page' => $page,
        ]);

        $resp = json_decode($response);
        
        if($resp->status = 200){
            Log::info("Response Get WA Accounts ".$this->base_url.$end_point." : ". $response);
        }else{
            Log::error("Response Get WA Accounts ".$this->base_url.$end_point." : ". $response);
        }

        return $resp;
    }


}
