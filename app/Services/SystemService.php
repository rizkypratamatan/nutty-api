<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Jobs\DatabaseAccountSyncJob;
use App\Jobs\DeleteOldTransactionJob;
use App\Jobs\PlayerTransactionJob;
use App\Jobs\PlayerTransactionSyncJob;
use App\Jobs\ReportDepositJob;
use App\Models\UnclaimedDepositQueue;
use App\Repository\DatabaseAccountModel;
use App\Repository\DatabaseLogModel;
use App\Repository\NexusPlayerTransactionModel;
use App\Repository\ReportUserModel;
use App\Repository\ReportWebsiteModel;
use App\Repository\SyncQueueModel;
use App\Repository\UnclaimedDepositQueueModel;
use App\Repository\UnclaimedDepositModel;
use App\Repository\UserModel;
use App\Repository\WebsiteModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
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


}
