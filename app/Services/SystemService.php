<?php

namespace App\Services;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Jobs\DatabaseAccountSyncJob;
use App\Jobs\DeleteOldTransactionJob;
use App\Jobs\PlayerTransactionJob;
use App\Jobs\PlayerTransactionSyncJob;
use App\Jobs\ReportDepositJob;
use App\Models\EmailQueue;
use App\Models\ReportUser;
use App\Models\ReportWebsite;
use App\Models\SmsQueue;
use App\Models\UnclaimedDepositQueue;
use App\Models\WaQueue;
use App\Repository\DatabaseAccountModel;
use App\Repository\DatabaseLogModel;
use App\Repository\DatabaseModel;
use App\Repository\EmailModel;
use App\Repository\NexusPlayerTransactionModel;
use App\Repository\ReportUserModel;
use App\Repository\ReportWebsiteModel;
use App\Repository\SettingModel;
use App\Repository\SMSModel;
use App\Repository\SyncQueueModel;
use App\Repository\UnclaimedDepositQueueModel;
use App\Repository\UnclaimedDepositModel;
use App\Repository\UserModel;
use App\Repository\WebsiteModel;
use App\Repository\WhatsappModel;
use App\Services\Gateway\EmailService;
use App\Services\Gateway\SMSService;
use App\Services\Gateway\WhatsappService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Operation\Watch;
use stdClass;


class SystemService {


    public static function deleteOldTransaction() {

        $result = new stdClass();
        $result->response = "Failed to find player transaction";
        $result->result = false;

        $websites = WebsiteModel::findAll();

        $delay = Carbon::now();

        foreach($websites as $value) {

            dispatch((new DeleteOldTransactionJob($value->_id)))->delay($delay->addMinutes(config("app.api.nexus.batch.delay")));

        }

    }


    private static function generateDeposit($databaseLog, $deposits, $unclaimedDeposit, $websiteId) {

        $databaseLog->status = "Deposited";
        DatabaseLogModel::update(DataComponent::initializeSystemAccount(), $databaseLog, $websiteId);

        $unclaimedDeposit->status = false;
        UnclaimedDepositModel::update(DataComponent::initializeSystemAccount(), $unclaimedDeposit, $websiteId);
        $nexusPlayerTransactionByReference = NexusPlayerTransactionModel::findOneByReference($unclaimedDeposit->reference, $websiteId);

        if(!empty($nexusPlayerTransactionByReference)) {

            $nexusPlayerTransactionByReference->claim = true;
            $nexusPlayerTransactionByReference->user = $databaseLog->user;
            NexusPlayerTransactionModel::update(DataComponent::initializeSystemAccount(), $nexusPlayerTransactionByReference, $websiteId);

        }

        if(array_key_exists(strval($databaseLog->user["_id"]), $deposits)) {

            if($unclaimedDeposit->type == "FirstDeposit") {

                $deposits[strval($databaseLog->user["_id"])]["total"] += 1;

            } else if($unclaimedDeposit->type == "Redeposit") {

                $deposits[strval($databaseLog->user["_id"])]["redeposit"] += 1;

            }

            array_push($deposits[strval($databaseLog->user["_id"])]["reference"], $nexusPlayerTransactionByReference->reference);

        } else {

            $total = 0;
            $redeposit = 0;

            if($unclaimedDeposit->type == "FirstDeposit") {

                $total = 1;

            } else if($unclaimedDeposit->type == "Redeposit") {

                $redeposit = 1;

            }

            $deposits[strval($databaseLog->user["_id"])] = [
                "redeposit" => $redeposit,
                "reference" => [$nexusPlayerTransactionByReference->reference],
                "total" => $total,
                "username" => $databaseLog->user["username"]
            ];

        }

        return $deposits;

    }


    public static function generateDepositReport() {

        $result = new stdClass();
        $result->response = "Failed to generate deposit report";
        $result->result = false;

        $unclaimedDepositQueueByStatus = UnclaimedDepositQueueModel::findOneByStatus("Pending");

        if(!empty($unclaimedDepositQueueByStatus)) {

            $websiteById = WebsiteModel::findOneById($unclaimedDepositQueueByStatus->website["_id"]);

            if(!empty($websiteById)) {

                if(!empty($websiteById->api["nexus"]["code"]) && !empty($websiteById->api["nexus"]["salt"]) && !empty($websiteById->api["nexus"]["url"])) {

                    $fromDate = $unclaimedDepositQueueByStatus->date . "T00:00:00";
                    $toDate = Carbon::createFromFormat("Y-m-d", $unclaimedDepositQueueByStatus->date)->addDays(1)->format("Y-m-d") . "T00:00:00";
                    $apiNexusPlayerTransaction = ApiNexusService::findPlayerTransaction($websiteById->api["nexus"]["code"], $fromDate, $toDate, $websiteById->api["nexus"]["salt"], $websiteById->api["nexus"]["url"]);

                    if($apiNexusPlayerTransaction->result) {

                        if($apiNexusPlayerTransaction->content->errorCode == 0) {

                            ApiNexusService::savePlayerTransaction($apiNexusPlayerTransaction->content->data->bankTransactionList, true, $websiteById->_id);

                            $unclaimedDepositQueueByStatus->status = "TransactionSaved";
                            UnclaimedDepositQueueModel::update(DataComponent::initializeSystemAccount(), $unclaimedDepositQueueByStatus);

                        }

                    }

                }

            }

        } else {

            $unclaimedDepositQueueByStatus = UnclaimedDepositQueueModel::findOneByStatus("TransactionSaved");

            if(!empty($unclaimedDepositQueueByStatus)) {

                $unclaimedDepositByStatus = UnclaimedDepositModel::findByStatusLimit(true, $unclaimedDepositQueueByStatus->website["_id"], 300);

                $deposits = [];

                if(!$unclaimedDepositByStatus->isEmpty()) {

                    foreach($unclaimedDepositByStatus as $value) {

                        $databaseAccountByUsername = DatabaseAccountModel::findOneByUsername($value->username, $unclaimedDepositQueueByStatus->website["_id"]);

                        if(!empty($databaseAccountByUsername)) {

                            $databaseLogByDatabaseId = DatabaseLogModel::findLastByDatabaseId($databaseAccountByUsername->database["_id"], $unclaimedDepositQueueByStatus->website["_id"]);

                            if(!empty($databaseLogByDatabaseId)) {

                                $userById = UserModel::findOneById($databaseLogByDatabaseId->user["_id"]);

                                if(!empty($userById)) {

                                    $deposits = self::generateDeposit($databaseLogByDatabaseId, $deposits, $value, $unclaimedDepositQueueByStatus->website["_id"]);

                                }

                            }

                        }

                        $unclaimedDepositById = UnclaimedDepositModel::findOneById($value->_id, $unclaimedDepositQueueByStatus->website["_id"]);

                        if(!empty($unclaimedDepositById)) {

                            $unclaimedDepositById->status = false;
                            UnclaimedDepositModel::update(DataComponent::initializeSystemAccount(), $unclaimedDepositById, $unclaimedDepositQueueByStatus->website["_id"]);

                        }

                    }

                    $totalDeposit = 0;
                    $account = null;

                    foreach($deposits as $key => $value) {

                        $reportUserByDateUserId = ReportUserModel::findOneByDateUserId(new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)->subDays(1)), $unclaimedDepositQueueByStatus->nucode, $key);

                        if(!empty($reportUserByDateUserId)) {

                            $statusNames = $reportUserByDateUserId->status["names"];
                            $statusTotals = $reportUserByDateUserId->status["totals"];

                            $index = array_search("Deposited", $statusNames);

                            if(gettype($index) == "integer") {

                                $statusTotals[$index] = $statusTotals[$index] + $value["total"];

                            } else {

                                array_push($statusNames, "Deposited");
                                array_push($statusTotals, $value["total"]);

                            }

                            $index = array_search("Redeposited", $statusNames);

                            if(gettype($index) == "integer") {

                                $statusTotals[$index] = $statusTotals[$index] + $value["redeposit"];

                            } else {

                                array_push($statusNames, "Redeposited");
                                array_push($statusTotals, $value["redeposit"]);

                            }

                            $account = UserModel::findOneById($key);

                            if(!empty($account)) {

                                $reportUserByDateUserId->status = [
                                    "names" => $statusNames,
                                    "totals" => $statusTotals
                                ];
                                ReportUserModel::update($account, $reportUserByDateUserId);

                            }

                        }

                        $totalDeposit += $value["total"];

                    }

                    if(!empty($account)) {

                        $reportWebsiteByDateUserId = ReportWebsiteModel::findOneByDateWebsiteId(new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)->subDays(1)), $unclaimedDepositQueueByStatus->nucode, $unclaimedDepositQueueByStatus->website["_id"]);

                        if(!empty($reportWebsiteByDateUserId)) {

                            $statusNames = $reportWebsiteByDateUserId->status["names"];
                            $statusTotals = $reportWebsiteByDateUserId->status["totals"];

                            $index = array_search("Deposited", $statusNames);

                            if(gettype($index) == "integer") {

                                $statusTotals[$index] += $totalDeposit;

                            } else {

                                array_push($statusNames, "Deposited");
                                array_push($statusTotals, $totalDeposit);

                            }

                            $reportWebsiteByDateUserId->status = [
                                "names" => $statusNames,
                                "totals" => $statusTotals
                            ];
                            ReportWebsiteModel::update($account, $reportWebsiteByDateUserId);

                            Log::debug("Deposit report updated");

                        }

                    }

                } else {

                    $unclaimedDepositQueueByStatus->status = "Done";
                    UnclaimedDepositQueueModel::update(DataComponent::initializeSystemAccount(), $unclaimedDepositQueueByStatus);

                }

                if(!config("app.debug")) {

                    $text = "Generate report " . $unclaimedDepositQueueByStatus->website["name"] . "\n" . json_encode($deposits);
                    DataComponent::sendTelegramBot($text);

                }

            }

        }

        return $result;

    }


    public static function generateUnclaimedDepositQueue($date) {

        $result = new stdClass();
        $result->response = "Failed to generate unclaimed deposit queue";
        $result->result = false;
        $result->total = 0;

        $websiteByStatusNotApiNexusSaltStart = WebsiteModel::findBySyncNotApiNexusSaltStart("", "", "Synced");

        if(!$websiteByStatusNotApiNexusSaltStart->isEmpty()) {

            foreach($websiteByStatusNotApiNexusSaltStart as $value) {

                $unclaimedDepositQueue = new UnclaimedDepositQueue();
                $unclaimedDepositQueue->date = Carbon::createFromFormat("Y-m-d", $date)->subDays(1)->format("Y-m-d");
                $unclaimedDepositQueue->nucode = $value->nucode;
                $unclaimedDepositQueue->status = "Pending";
                $unclaimedDepositQueue->website = [
                    "_id" => DataComponent::initializeObjectId($value->_id),
                    "name" => $value->name
                ];
                UnclaimedDepositQueueModel::insert(DataComponent::initializeSystemAccount(), $unclaimedDepositQueue);

                $result->total++;

                usleep(1000);

            }

        }

        $result->response = "Unclaimed deposit queue generated";
        $result->result = true;

        return $result;

    }

    // public static function syncWebsiteTransaction() {

    //     $result = new stdClass();
    //     $result->response = "Failed to sync website transaction";
    //     $result->result = false;

    //     $syncQueueByStatus = SyncQueueRepository::findOneByStatus("OnGoing");

    //     if(!empty($syncQueueByStatus)) {

    //         $websiteById = WebsiteRepository::findOneById($syncQueueByStatus->website["_id"]);

    //         if(!empty($websiteById)) {

    //             if(!empty($websiteById->api["nexus"]["code"]) && !empty($websiteById->api["nexus"]["salt"]) && !empty($websiteById->api["nexus"]["url"])) {

    //                 $date = Carbon::createFromDate($syncQueueByStatus->date->toDateTime());
    //                 $dateStart = $date->format("Y-m-d") . "T00:00:00";
    //                 $date = $date->addDays(1);
    //                 $dateEnd = $date->format("Y-m-d") . "T00:00:00";
    //                 $apiNexusPlayerTransaction = ApiNexusService::findPlayerTransaction($websiteById->api["nexus"]["code"], $dateStart, $dateEnd, $websiteById->api["nexus"]["salt"], $websiteById->api["nexus"]["url"]);

    //                 if($apiNexusPlayerTransaction->result) {

    //                     ApiNexusService::savePlayerTransaction($apiNexusPlayerTransaction->content->data->bankTransactionList, false, $websiteById->_id);

    //                     $syncQueueByStatus->date = new UTCDateTime($date);
    //                     SyncQueueRepository::update(DataComponent::initializeSystemAccount(), $syncQueueByStatus);

    //                     $text = "Nucode : " . $websiteById->nucode . "\n" . "Name : " . $websiteById->name . "\n";

    //                     if($date->gte(Carbon::now())) {

    //                         SyncQueueRepository::delete($syncQueueByStatus);

    //                         $websiteById->sync = "Synced";
    //                         WebsiteRepository::update(DataComponent::initializeSystemAccount(), $websiteById);

    //                         if(!config("app.debug")) {

    //                             DataComponent::sendTelegramBot($text . "Status : Transaction synced completed");

    //                         }

    //                     } else {

    //                         if(!config("app.debug")) {

    //                             DataComponent::sendTelegramBot($text . "Status : Transaction synced until " . $syncQueueByStatus->date->toDateTime()->format('Y-m-d'));

    //                         }

    //                     }

    //                 }

    //             }

    //         }

    //         $result->response = "Player transaction synced";
    //         $result->result = true;

    //     }

    //     return $result;

    // }


    public static function syncWebsiteTransaction() {

        $result = new stdClass();
        $result->response = "Failed to sync website transaction";
        $result->result = false;

        $syncQueueByStatus = SyncQueueModel::findOneByStatus("OnGoing");

        if(!empty($syncQueueByStatus)) {

            $websiteById = WebsiteModel::findOneById($syncQueueByStatus->website["_id"]);

            if(!empty($websiteById)) {

                if(!empty($websiteById->api["nexus"]["code"]) && !empty($websiteById->api["nexus"]["salt"]) && !empty($websiteById->api["nexus"]["url"])) {

                    $date = Carbon::createFromDate($syncQueueByStatus->date->toDateTime());
                    $dateStart = $date->format("Y-m-d") . "T00:00:00";
                    $date = $date->addDays(1);
                    $dateEnd = $date->format("Y-m-d") . "T00:00:00";
                    $apiNexusPlayerTransaction = ApiNexusService::findPlayerTransaction($websiteById->api["nexus"]["code"], $dateStart, $dateEnd, $websiteById->api["nexus"]["salt"], $websiteById->api["nexus"]["url"]);

                    if($apiNexusPlayerTransaction->result && isset($apiNexusPlayerTransaction->content->data)) {
                        Log::info("response api nexus = ". json_encode($apiNexusPlayerTransaction));

                        ApiNexusService::savePlayerTransaction($apiNexusPlayerTransaction->content->data->bankTransactionList, false, $websiteById->_id);

                        $syncQueueByStatus->date = new UTCDateTime($date);
                        SyncQueueModel::update(DataComponent::initializeSystemAccount(), $syncQueueByStatus);

                        $text = "Nucode : " . $websiteById->nucode . "\n" . "Name : " . $websiteById->name . "\n";

                        if($date->gte(Carbon::now())) {

                            SyncQueueModel::delete($syncQueueByStatus);

                            $websiteById->sync = "Synced";
                            WebsiteModel::update(DataComponent::initializeSystemAccount(), $websiteById);

                            if(!config("app.debug")) {

                                DataComponent::sendTelegramBot($text . "Status : Transaction synced completed");

                            }

                        } else {

                            if(!config("app.debug")) {

                                DataComponent::sendTelegramBot($text . "Status : Transaction synced until " . $syncQueueByStatus->date->toDateTime()->format('Y-m-d'));

                            }

                        }

                    }

                }

            }

            $result->response = "Player transaction synced";
            $result->result = true;

        }

        return $result;

    }

    public static function processSmsQueue(){

        //get availabel nucode
        $nucodes = UserModel::getAvailableNucode();

        foreach($nucodes as $nucode){
            //get secret key gateway
            $gw_secret = SettingModel::getSettingByName('gateway_apikey', $nucode);
            if($gw_secret){
                $smsQueueCount = SmsQueue::where('status', 'queue')->where('nucode', $nucode)->count();
                if($smsQueueCount > 0){
                    //get device
                    $service = new SMSService();
                    $device = $service->getDevices($gw_secret['value']);

                    if($device['status'] == 200){
                        $accountCount = count($device['data']);
                        if($accountCount > 0){
                            $limit = $accountCount * 3;
                            $smsQueue = SmsQueue::where('status', 'queue')
                                                        ->where('nucode', $nucode)
                                                        ->take($limit)
                                                        ->offset(0)
                                                        ->orderBy('created.timestamp', 'asc')
                                                        ->get()
                                                        ->toArray();
                            
                            if ($smsQueueCount > 3) {
                                
                                if ($smsQueueCount <= $accountCount) {
                                    $accountCount = $smsQueueCount;
                                    $devider = $smsQueueCount / $accountCount;
                                } else {
                                    $devider = round($limit / $accountCount);
                                }
                
                                $smsQueue = array_chunk($smsQueue, $devider);
                            } else {
                                $accountCount = 1;
                            }
                
                            for ($i = 0; $i < $accountCount; $i++) {
                                $device_id = $device['data'][$i]['unique'];
                                
                                if(!isset($smsQueue[$i]['message'])){
                                    foreach($smsQueue[$i] as $val){
                                        $account = UserModel::findOneById($val['created']['user']['_id']->__toString());
                                        $message = $service->initializeData($val['message'], $device_id, $val['number']);
                                        $response = $service->sendSms($message, $gw_secret['value']);
                                        $result = SMSModel::insert($message, $account);

                                        $websitebyId = WebsiteModel::findOneById($val['website']['_id']->__toString());
                                        //update database
                                        $database = DatabaseModel::findOneById($val['database']['_id']->__toString(), $websitebyId->_id);
                                        $database->status = "Processed";
                                        $database->lastSmsDate = date("Y-m-d");
                                        $database->save();

                                        $databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $account->_id, $websitebyId->_id);

                                        if(!empty($databaseLog)) {

                                            $databaseLog->status = "FollowUp";
                                            DatabaseLogModel::update($account, $databaseLog, $websitebyId->_id);
                        
                                        } else {
                                            $databaseLog = new DatabaseLog();
                                            $databaseLog->database = [
                                                "_id" => DataComponent::initializeObjectId($database->_id),
                                                "name" => $database->name
                                            ];
                                            $databaseLog->status = "FollowUp";
                                            $databaseLog->user = [
                                                "_id" => DataComponent::initializeObjectId($account->_id),
                                                "avatar" => $account->avatar,
                                                "name" => $account->name,
                                                "username" => $account->username
                                            ];
                                            $databaseLog->website = [
                                                "_id" => DataComponent::initializeObjectId($websitebyId->_id),
                                                "name" => $val['website']['name']
                                            ];
                                            DatabaseLogModel::insert($account, $databaseLog, $websitebyId->_id);
                        
                                        }
                                        
                                        self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), "FollowUp", $websitebyId);

                                        //remove queue
                                        SmsQueue::destroy($val['_id']);
                                    } 
                                }else{
                                    $account = UserModel::findOneById($smsQueue[$i]['created']['user']['_id']->__toString());
                                    $message = $service->initializeData($smsQueue[$i]['message'], $device_id, $smsQueue[$i]['number']);
                                    $response = $service->sendSms($message, $gw_secret['value']);
                                    $result = SMSModel::insert($message, $account);

                                    $websitebyId = WebsiteModel::findOneById($smsQueue[$i]['website']['_id']->__toString());

                                    //update database
                                    $database = DatabaseModel::findOneById($smsQueue[$i]['database']['_id']->__toString(), $websitebyId->_id);
                                    $database->status = "Processed";
                                    $database->lastSmsDate = date("Y-m-d");
                                    $database->save();

                                    $databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $account->_id, $websitebyId->_id);

                                        if(!empty($databaseLog)) {

                                            $databaseLog->status = "FollowUp";
                                            DatabaseLogModel::update($account, $databaseLog, $websitebyId->_id);
                        
                                        } else {
                                            $databaseLog = new DatabaseLog();
                                            $databaseLog->database = [
                                                "_id" => DataComponent::initializeObjectId($database->_id),
                                                "name" => $database->name
                                            ];
                                            $databaseLog->status = "FollowUp";
                                            $databaseLog->user = [
                                                "_id" => DataComponent::initializeObjectId($account->_id),
                                                "avatar" => $account->avatar,
                                                "name" => $account->name,
                                                "username" => $account->username
                                            ];
                                            $databaseLog->website = [
                                                "_id" => DataComponent::initializeObjectId($websitebyId->_id),
                                                "name" => $smsQueue[$i]['website']['name']
                                            ];
                                            DatabaseLogModel::insert($account, $databaseLog, $websitebyId->_id);
                        
                                        }
                                        self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), "FollowUp", $websitebyId);
                                        

                                    //remove queue
                                    SmsQueue::destroy($smsQueue[$i]['_id']);
                                }   
                            }
                        }else{
                            Log::info("No SMS Account Found nucode ". $nucode);
                        }
                    }else{
                        Log::info("Failed to get Device Nucode ". $nucode);
                    }
                    
                }else{
                    Log::info("No SMS Queue Found For Nucode ". $nucode);    
                }
            }
        }
    }

    public static function processWaQueue(){

        //get availabel nucode
        $nucodes = UserModel::getAvailableNucode();

        foreach($nucodes as $nucode){
            
            $dataQueueCount = WaQueue::where('status', 'queue')
                                        ->where('nucode', $nucode)
                                        ->count();
            $gw_secret = SettingModel::getSettingByName('gateway_apikey', $nucode);
            if($dataQueueCount > 0){
                //get device
                $service = new WhatsappService();
                $waAccount = $service->getAccounts($gw_secret['value']);
                
                if($waAccount['status'] == 200){
                    $accountCount = count($waAccount['data']);
                    if($accountCount > 0){
                        $limit = $accountCount * 3;
                        $dataQueue = WaQueue::where('status', 'queue')
                                                    ->where('nucode', $nucode)
                                                    ->take($limit)
                                                    ->offset(0)
                                                    ->orderBy('created.timestamp', 'asc')
                                                    ->get()
                                                    ->toArray();
                        if ($dataQueueCount > 3) {
                            if ($dataQueueCount <= $accountCount) {
                                $accountCount = $dataQueueCount;
                                $devider = $dataQueueCount / $accountCount;
                            } else {
                                $devider = round($limit / $accountCount);
                            }
            
                            $dataQueue = array_chunk($dataQueue, $devider);
                        } else {
                            $accountCount = 1;
                        }
                        
                        for ($i = 0; $i < $accountCount; $i++) {
                            $device_id = $waAccount['data'][$i]['id'];
                            if(!isset($dataQueue[$i]['message'])){
                                foreach($dataQueue[$i] as $val){
                                    $account = UserModel::findOneById($val['created']['user']['_id']->__toString());
                                    $message = $service->initializeData($val['message'], $device_id, $val['number']);
                                    $response = $service->send($message, $gw_secret['value']);
                                    $result = WhatsappModel::insert($message, $account);

                                    $websitebyId = WebsiteModel::findOneById($val['website']['_id']->__toString());

                                    //update database
                                    $database = DatabaseModel::findOneById($val['database']['_id']->__toString(), $websitebyId->_id);
                                    $database->status = "Processed";
                                    $database->lastWaDate = date("Y-m-d");
                                    $database->save();

                                    $databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $account->_id, $websitebyId->_id);

                                    if(!empty($databaseLog)) {

                                        $databaseLog->status = "FollowUp";
                                        DatabaseLogModel::update($account, $databaseLog, $websitebyId->_id);
                    
                                    } else {
                                        $databaseLog = new DatabaseLog();
                                        $databaseLog->database = [
                                            "_id" => DataComponent::initializeObjectId($database->_id),
                                            "name" => $database->name
                                        ];
                                        $databaseLog->status = "FollowUp";
                                        $databaseLog->user = [
                                            "_id" => DataComponent::initializeObjectId($account->_id),
                                            "avatar" => $account->avatar,
                                            "name" => $account->name,
                                            "username" => $account->username
                                        ];
                                        $databaseLog->website = [
                                            "_id" => DataComponent::initializeObjectId($websitebyId->_id),
                                            "name" => $val['website']['name']
                                        ];
                                        DatabaseLogModel::insert($account, $databaseLog, $websitebyId->_id);
                    
                                    } 
                                    self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), "FollowUp", $websitebyId);

                                    //remove queue
                                    WaQueue::destroy($val['_id']);
                                }    
                            }else{
                                $account = UserModel::findOneById($dataQueue[$i]['created']['user']['_id']->__toString());
                                $message = $service->initializeData($dataQueue[$i]['message'], $device_id, $dataQueue[$i]['number']);
                                $response = $service->send($message, $gw_secret);
                                $result = WhatsappModel::insert($message, $account);

                                $websitebyId = WebsiteModel::findOneById($dataQueue[$i]['website']['_id']->__toString());

                                //update database
                                $database = DatabaseModel::findOneById($dataQueue[$i]['database']['_id']->__toString(), $websitebyId->_id);
                                $database->status = "Processed";
                                $database->lastWaDate = date("Y-m-d");
                                $database->save();

                                $databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $account->_id, $websitebyId->_id);

                                if(!empty($databaseLog)) {

                                    $databaseLog->status = "FollowUp";
                                    DatabaseLogModel::update($account, $databaseLog, $websitebyId->_id);
                
                                } else {
                                    $databaseLog = new DatabaseLog();
                                    $databaseLog->database = [
                                        "_id" => DataComponent::initializeObjectId($database->_id),
                                        "name" => $database->name
                                    ];
                                    $databaseLog->status = "FollowUp";
                                    $databaseLog->user = [
                                        "_id" => DataComponent::initializeObjectId($account->_id),
                                        "avatar" => $account->avatar,
                                        "name" => $account->name,
                                        "username" => $account->username
                                    ];
                                    $databaseLog->website = [
                                        "_id" => DataComponent::initializeObjectId($websitebyId->_id),
                                        "name" => $dataQueue[$i]['website']['name']
                                    ];
                                    DatabaseLogModel::insert($account, $databaseLog, $websitebyId->_id);
                
                                } 
                                self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), "FollowUp", $websitebyId);

                                //remove queue
                                WaQueue::destroy($dataQueue[$i]['_id']);
                            }
                        }
                    }else{
                        Log::info("No WA Account Found nucode ". $nucode);
                    }
                }else{
                    Log::info("Failed to get WA Account nucode ". $nucode);
                }
            }else{
                Log::info("No WA Queue Found for Nucode ". $nucode);    
            }
                        
        }
    }

    public static function processEmailQueue(){

                //get availabel nucode
        $nucodes = UserModel::getAvailableNucode();
        foreach($nucodes as $nucode){

            $setting = SettingModel::getSettingByNucode($nucode);

            $service = new EmailService();
            $dataQueueCount = EmailQueue::where('status', 'queue')
                                        ->where('nucode', $nucode)
                                        ->count();
            
            if($dataQueueCount > 0){
                $dataQueue = EmailQueue::where('status', 'queue')
                                            ->where('nucode', $nucode)
                                            ->take(10)
                                            ->offset(0)
                                            ->orderBy('created.timestamp', 'asc')
                                            ->get()
                                            ->toArray();

                foreach($dataQueue as $val){
                    $account = UserModel::findOneById($val['created']['user']['_id']->__toString());
                    $message = $service->initializeDataEmail($val['subject'], $val['body'], $val['email'], $setting->from_name, $setting->from_email);
                    $response = $service->sendEmail($val['email'], $message->toArray(), $setting);
                    $result = EmailModel::insert($message, $account);

                    $websitebyId = WebsiteModel::findOneById($val['website']['_id']->__toString());

                    //update database
                    $database = DatabaseModel::findOneById($val['database']['_id']->__toString(), $websitebyId->_id);
                    $database->status = "Processed";
                    $database->lastEmailDate = date("Y-m-d");
                    $database->save();

                    $databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $account->_id, $websitebyId->_id);

                    if(!empty($databaseLog)) {

                        $databaseLog->status = "FollowUp";
                        DatabaseLogModel::update($account, $databaseLog, $websitebyId->_id);

                    } else {
                        $databaseLog = new DatabaseLog();
                        $databaseLog->database = [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $databaseLog->status = "FollowUp";
                        $databaseLog->user = [
                            "_id" => DataComponent::initializeObjectId($account->_id),
                            "avatar" => $account->avatar,
                            "name" => $account->name,
                            "username" => $account->username
                        ];
                        $databaseLog->website = [
                            "_id" => DataComponent::initializeObjectId($websitebyId->_id),
                            "name" => $val['website']['name']
                        ];
                        DatabaseLogModel::insert($account, $databaseLog, $websitebyId->_id);

                    } 

                    self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), "FollowUp", $websitebyId);

                    //remove queue
                    EmailQueue::destroy($val['_id']);
                }    
            }else{
                Log::info("No Email Queue Found");    
            }
        }
    }

    public static function generateReport($account, $date, $status, $website) {

        $reportUserByDateUserId = ReportUserModel::findOneByDateUserId($date, $account->nucode, $account->_id);

        // print_r($reportUserByDateUserId);die;

        if(!empty($reportUserByDateUserId)) {

            $reportUserByDateUserId->status = self::initializeStatusData($reportUserByDateUserId, $status);
            $reportUserByDateUserId->total += 1;
            $reportUserByDateUserId->website = self::initializeWebsiteData($reportUserByDateUserId, $website);
            ReportUserModel::update($account, $reportUserByDateUserId);

        } else {

            $reportUser = new ReportUser();
            $reportUser->date = $date;
            $reportUser->status = [
                "names" => [$status],
                "totals" => [1]
            ];
            $reportUser->total = 1;
            $reportUser->user = [
                "_id" => DataComponent::initializeObjectId($account->_id),
                "avatar" => $account->avatar,
                "name" => $account->name,
                "username" => $account->username
            ];
            $reportUser->website = [
                "ids" => [$website->_id],
                "names" => [$website->name],
                "totals" => [1]
            ];
            ReportUserModel::insert($account, $reportUser);

        }

        $reportWebsiteByDateWebsiteId = ReportWebsiteModel::findOneByDateWebsiteId($date, $account->nucode, $website->_id);

        if(!empty($reportWebsiteByDateWebsiteId)) {

            $reportWebsiteByDateWebsiteId->status = self::initializeStatusData($reportWebsiteByDateWebsiteId, $status);
            $reportWebsiteByDateWebsiteId->total += 1;
            ReportWebsiteModel::update($account, $reportWebsiteByDateWebsiteId);

        } else {

            $reportWebsite = new ReportWebsite();
            $reportWebsite->date = $date;
            $reportWebsite->status = [
                "names" => [$status],
                "totals" => [1]
            ];
            $reportWebsite->total = 1;
            $reportWebsite->website = [
                "_id" => DataComponent::initializeObjectId($website->_id),
                "name" => $website->name
            ];
            ReportWebsiteModel::insert($account, $reportWebsite);

        }

    }

    private static function initializeStatusData($data, $status) {

        $result = $data->status;

        $index = array_search($status, $data->status["names"]);

        if(gettype($index) == "integer") {

            $result["totals"][$index]++;

        } else {

            array_push($result["names"], $status);
            array_push($result["totals"], 1);

        }

        return $result;

    }

    private static function initializeWebsiteData($data, $website) {
        $result = $data->website;

        $index = array_search($website->_id, $data->website["ids"]);

        if(gettype($index) == "integer") {

            $result["totals"][$index]++;

        } else {

            array_push($result["ids"], $website->_id);
            array_push($result["names"], $website->name);
            array_push($result["totals"], 1);

        }

        return $result;

    }
}
