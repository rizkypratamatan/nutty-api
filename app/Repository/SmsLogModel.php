<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Services\Gateway\SMSService;
use Illuminate\Support\Facades\DB;

class SmsLogModel
{
    protected $service;
    protected $user;
    protected $request;

    public function __construct($request)
    {   
        $this->service = new SMSService();
        $this->request = $request;
    }

    public function getAll($limit=10, $offset=0)
    {   
        $user = AuthenticationComponent::toUser($this->request);
        return DB::table("smsLogs_".$user->_id)
                        ->take($limit)
                        ->skip($offset)
                        ->get();
    }

    public function delete($id)
    {
        $user = AuthenticationComponent::toUser($this->request);
        return DB::table("smsLogs_".$user->_id)
                    ->where('_id', $id)
                    ->delete();
        
    }

    public function getById($id)
    {
        $user = AuthenticationComponent::toUser($this->request);
        return DB::table("smsLogs_".$user->_id)
                    ->where('_id', $id)
                    ->first();
    }

    public function sendSingle()
    {
        $device = $this->service->getDevices();

        if($device['status'] == 200){
            $message = $this->service->initializeSingleData($this->request->message, $device['data'][0]['unique'], $this->request->phone);
            $response = $this->service->processSingleChat($message);
            $this->insertDB($message);
        }else{
            $response = [
                'result' => false,
                'response' => "SMS service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

    public function sendBulk()
    {   
        //get wa accounts
        $device = $this->service->getDevices();
        
        if($device['status'] == 200){
            $accountCount = count($device['data']);

            $numbers = explode(",", $this->request->numbers);
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
            }else{
                $accountCount = 1;
            }

            for($i=0;$i<$accountCount;$i++){
                $device_id = $device['data'][$i]['unique'];
                if(is_array($numbers[$i])){    
                    $bulk = $this->service->initializeBulkData($this->request, $device_id, implode(",", $numbers[$i]));
                    //proses chat
                    $this->service->processBulkChat($bulk);

                    //save DB
                    foreach($numbers[$i] as $recepient){
                        $data = $this->service->initializeSingleData($this->request->message, $device_id, $recepient);
                        $this->insertDB($data);
                    }
                }else{
                    $bulk = $this->service->initializeBulkData($this->request, $device_id, implode(",", $numbers));
                    //proses chat
                    $this->service->processBulkChat($bulk);

                    //save DB
                    foreach($numbers[$i] as $recepient){
                        $data = $this->service->initializeSingleData($this->request->message, $device_id, $recepient);
                        $this->insertDB($data);
                    }
                }
            }

            $response = [
                'result' => true,
                'response' => "Message chats has been queued!",
                'data' => false
            ];

        }else{
            $response = [
                'result' => false,
                'response' => "Message service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

    public function insertDB($data)
    {
        $user = AuthenticationComponent::toUser($this->request);
        unset($data['secret']);
        unset($data['campaign']);
        unset($data['numbers']);
            
        $data['status'] = "queued";
        $data['created'] = DataComponent::initializeTimestamp($user);
        $data['modified'] = DataComponent::initializeTimestamp($user);
        
        return DB::table('smsLogs_'.$this->user->_id)->insert($data);
    }

    public function sendTestSingle()
    {
        $device = $this->service->getDevices();

        if($device['status'] == 200){
            $message = $this->service->initializeSingleData($this->request->message, $device['data'][0]['unique'], $this->request->phone);
            $response = $this->service->processSingleChat($message);
            // $this->insertDB($message);
        }else{
            $response = [
                'result' => false,
                'response' => "SMS service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

    public function sendTestBulk()
    {   
        //get wa accounts
        $device = $this->service->getDevices();
        
        if($device['status'] == 200){
            $accountCount = count($device['data']);

            $numbers = explode(",", $this->request->numbers);
            $total_number = count($numbers);

            if($total_number > 3){
                if($total_number <= $accountCount){
                    $accountCount = $total_number;
                    $devider = $total_number/$accountCount;
                    
                }else{
                    $devider = round($total_number/$accountCount);
                }
                $numbers = array_chunk($numbers, $devider);
            }else{
                $accountCount = 1;
            }
            for($i=0;$i<$accountCount;$i++){
                $device_id = $device['data'][$i]['unique'];

                if(is_array($numbers[$i])){
                    $bulk = $this->service->initializeBulkData($this->request, $device_id, implode(",", $numbers[$i]));
                    //proses chat
                    $this->service->processBulkChat($bulk);

                    //save DB
                    foreach($numbers[$i] as $recepient){
                        $data = $this->service->initializeSingleData($this->request->message, $device_id, $recepient);
                        // $this->insertDB($data);
                    }
                }else{
                    $bulk = $this->service->initializeBulkData($this->request, $device_id, implode(",", $numbers));
                    //proses chat
                    $this->service->processBulkChat($bulk);

                    //save DB
                    foreach($numbers as $recepient){
                        $data = $this->service->initializeSingleData($this->request->message, $device_id, $recepient);
                        // $this->insertDB($data);
                    }
                }

                
            }

            $response = [
                'result' => true,
                'response' => "Message chats has been queued!",
                'data' => false
            ];

        }else{
            $response = [
                'result' => false,
                'response' => "Message service currently unavailable",
                'data' => false
            ];
        }

        return $response;
    }

}
