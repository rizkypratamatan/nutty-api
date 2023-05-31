<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Services\Gateway\WhatsappService;
use Illuminate\Support\Facades\DB;

class WhatsappLogModel
{
    protected $service;
    protected $user;
    protected $request;

    public function __construct($request)
    {   
        $this->service = new WhatsappService();
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function getAllChat($limit=10, $offset=0)
    {   
        $data = DB::table("whatsappLogs_".$this->user->_id)
                ->take($limit)
                ->skip($offset)
                ->get();

        return $data;
    }

    public function deleteChat($id)
    {
        return DB::table("whatsappLogs_".$this->user->_id)
                ->where('_id', $id)
                ->delete();
    }

    public function getChatById($id)
    {
        return DB::table("whatsappLogs_".$this->user->_id)
                        ->where('_id', $id)
                        ->first();
    }

    public function sendSingleChat()
    {
        $account = $this->service->getAccounts();

        if($account['status'] == 200){
            $chat = $this->service->initializeSingleData($this->request, $account['data'][0]['id'], $this->request->recipient);
            $resp = $this->service->processSingleChat($chat);
            $this->insertDB($chat);

            $response = $resp;
        }else{
            $response = [
                'result' => false,
                'response' => "Whatsapp service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

    public function sendBulkChat()
    {   
        //get wa accounts
        $account = $this->service->getAccounts();
        
        if($account['status'] == 200){
            $accountCount = count($account['data']);

            $numbers = explode(",", $this->request->recipients);
            $total_number = count($numbers);

            if($total_number > 3){
                //
                if($total_number <= $accountCount){
                    $accountCount = $total_number;
                    $devider = $total_number/$accountCount;
                    
                }else{
                    $devider = round($total_number/$accountCount);
                }
                $numbers = array_chunk($numbers, $devider);
            }

            for($i=0;$i<$accountCount;$i++){
                $device_id = $account['data'][$i]['id'];
                $bulk = $this->service->initializeBulkChat($this->request, $device_id, implode(",", $numbers[$i]));
                //proses chat
                $this->service->processBulkChat($bulk);

                //save DB
                foreach($numbers[$i] as $recepient){
                    $data = $this->service->initializeSingleChat($this->request, $device_id, $recepient);
                    $this->insertDB($data);
                }
            }

            $response = [
                'result' => true,
                'response' => "WhatsApp chats has been queued!",
                'data' => false
            ];

        }else{
            $response = [
                'result' => false,
                'response' => "Whatsapp service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

    public function insertDB($data)
    {
        unset($data['recipients']);
        unset($data['group']);
        unset($data['secret']);

        $data['status'] = "queued";
        $data['created'] = DataComponent::initializeTimestamp($this->user);
        $data['modified'] = DataComponent::initializeTimestamp($this->user);;

        return DB::table('whatsappLogs_'.$this->user->_id)->insert($data);
    }

    // public function deleteReceivedChat($id)
    // {   
    //     $end_point = "/api/delete/wa.received";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'id' => $id
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Delete Received WA ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Delete Received WA ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function deleteSentChat($id)
    // {   
    //     $end_point = "/api/delete/wa.sent";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'id' => $id
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Delete Sent WA ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Delete Sent WA ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function deleteWhatsappCampaign($id)
    // {   
    //     $end_point = "/api/delete/wa.campaign";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'id' => $id
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Delete WA Campaign ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Delete WA Campaign ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function getPendingChats($limit=10, $page=1)
    // {   
    //     $end_point = "/api/get/wa.pending";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'limit' => $limit,
    //         'page' => $page,
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Get WA Pending ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Get WA Pending ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function getReceivedChats($limit=10, $page=1)
    // {   
    //     $end_point = "/api/get/wa.received";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'limit' => $limit,
    //         'page' => $page,
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Get WA Received ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Get WA Received ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function getSentChats($limit=10, $page=1)
    // {   
    //     $end_point = "/api/get/wa.sent";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'limit' => $limit,
    //         'page' => $page,
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Get WA Sent ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Get WA Sent ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function getWhatsappCampaign($limit=10, $page=1)
    // {   
    //     $end_point = "/api/get/wa.campaigns";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'limit' => $limit,
    //         'page' => $page,
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Get WA Campaigns ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Get WA Campaigns ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function getWhatsappQrImage($token, $qrstring)
    // {   
    //     $end_point = "/api/get/wa.qr";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'token' => $token,
    //         'qrstring' => $qrstring
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Get WA QR ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Get WA QR ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function startWhatsappCampaign($campaign)
    // {   

    //     $end_point = "/api/remote/start.chats";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'campaign' => $campaign
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Start Wa Campaign ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Start Wa Campaign ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

    // public function stopWhatsappCampaign($campaign)
    // {   
    //     $end_point = "/api/remote/stop.chats";
    //     $response = Http::get($this->base_url.$end_point, [
    //         'secret' => $this->secret,
    //         'campaign' => $campaign
    //     ]);

    //     $resp = json_decode($response);
        
    //     if($resp->status = 200){
    //         Log::info("Response Stop Wa Campaign ".$this->base_url.$end_point." : ". $response);
    //     }else{
    //         Log::error("Response Stop Wa Campaign ".$this->base_url.$end_point." : ". $response);
    //     }

    //     return $resp;
    // }

}
