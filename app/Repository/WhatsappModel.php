<?php

namespace App\Repository;

use App\Models\Whatsapp;
use App\Services\Gateway\WhatsappService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WhatsappModel
{
    protected $service;

    public function __construct()
    {   
        $this->service = new WhatsappService();
    }

    public function getAllChat($limit=10, $offset=0, $auth=null)
    {   
        if($auth){
            return Whatsapp::where("created.user._id", $auth->id)
                            ->take($limit)
                            ->skip($offset)
                            ->get();
        }else{
            return Whatsapp::take($limit)
                            ->skip($offset)
                            ->get();
        }
        
    }

    public static function deleteChat($id, $auth=null)
    {
        if($auth){
            return Whatsapp::where('_id', $id)->where("created.user._id", $auth->id)->delete();
        }else{
            return Whatsapp::where('_id', $id)->delete();
        }
        
    }

    public function getChatById($id, $auth=null)
    {
        if($auth){
            return Whatsapp::where('_id', $id)->where("created.user._id", $auth->id)->first();
        }else{
            return Whatsapp::where('_id', $id)->first();
        }
        
    }

    public function sendSingleChat($request)
    {
        //get device
        $model = new WhatsappModel();
        $account = $model->getAccounts();

        if($account == 200){
            $acc = json_decode($account, true);
            $chat = $this->initializeData($request, $acc['data'][0]['id'], $request->recipient);

            $this->service->processSingleChat($chat);
            $this->insertDB($chat);

            $response = [
                'result' => true,
                'response' => "WhatsApp chat has been queued!",
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

    public function sendBulkChat($request)
    {   
        //get wa accounts
        $account = $this->service->getAccounts();
        if($account == 200){
            $acc = json_decode($account, true);
            $accountCount = count(json_decode($acc['data']));

            $numbers = explode(",", $request->recipients);
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

            $bulk = [
                "account" => "",
                "campaign" => !empty($request->campaign)?$request->campaign:"",
                "recipients" => "",
                "groups" => "",
                "type" => !empty($request->type)?$request->type:"text",
                "message" => !empty($request->message)?$request->message:"",
                "media_file" => !empty($request->media_file)?$request->media_file:"",
                "media_url" => !empty($request->media_url)?$request->media_url:"",
                "media_type" => !empty($request->media_type)?$request->media_type:"",
                "document_file" => !empty($request->document_file)?$request->document_file:"",
                "document_url" => !empty($request->document_url)?$request->document_url:"",
                "document_type" => !empty($request->document_type)?$request->document_type:"",
                "button_1" => !empty($request->button_1)?$request->button_1:"",
                "button_2" => !empty($request->button_2)?$request->button_2:"",
                "button_3" => !empty($request->button_3)?$request->button_3:"",
                "list_title" => !empty($request->list_title)?$request->list_title:"",
                "menu_title" => !empty($request->menu_title)?$request->menu_title:"",
                "footer" => !empty($request->footer)?$request->footer:"",
                "format" => !empty($request->format)?$request->format:"",
                "shortener" => !empty($request->shortener)?$request->shortener:"",
            ];

            for($i=0;$i<$accountCount;$i++){
                $device_id = $acc['data'][$i]['id'];
                $bulk["account"] = $device_id;
                $bulk["recipients"] = implode(",", $numbers[$i]);

                //proses chat
                $this->service->processBulkChat($bulk);

                //save DB
                foreach($numbers[$i] as $recepient){
                    $data = $this->initializeData($request, $device_id, $recepient);
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

    public function initializeData($request, $account, $recipient)
    {
        $chat = [
            "account" => $account,
            "campaign" => !empty($request->campaign)?$request->campaign:"",
            "recipient" => $recipient,
            "type" => !empty($request->type)?$request->type:"text",
            "message" => !empty($request->message)?$request->message:"",
            "media_file" => !empty($request->media_file)?$request->media_file:"",
            "media_url" => !empty($request->media_url)?$request->media_url:"",
            "media_type" => !empty($request->media_type)?$request->media_type:"",
            "document_file" => !empty($request->document_file)?$request->document_file:"",
            "document_url" => !empty($request->document_url)?$request->document_url:"",
            "document_type" => !empty($request->document_type)?$request->document_type:"",
            "button_1" => !empty($request->button_1)?$request->button_1:"",
            "button_2" => !empty($request->button_2)?$request->button_2:"",
            "button_3" => !empty($request->button_3)?$request->button_3:"",
            "list_title" => !empty($request->list_title)?$request->list_title:"",
            "menu_title" => !empty($request->menu_title)?$request->menu_title:"",
            "footer" => !empty($request->footer)?$request->footer:"",
            "format" => !empty($request->format)?$request->format:"",
            "shortener" => !empty($request->shortener)?$request->shortener:"",
        ];

        return $chat;
    }

    public function insertDB($data)
    {
        unset($data['recipients']);
        unset($data['group']);
        unset($data['secret']);

        //save to db
        $mytime = Carbon::now();
            
        $data['status'] = "queued";

        $data['created'] = [
            "timestamp" => $mytime->toDateTimeString()
        ];

        $data['modified'] = [
            "timestamp" => $mytime->toDateTimeString()
        ];

        return DB::table('userGroup')->insert($data);
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
