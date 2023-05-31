<?php

namespace App\Services;

use App\Components\LogComponent;
use App\Components\RestComponent;
use App\Repository\NexusPlayerTransactionModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;


class ApiNexusService {


    public static function findPlayerTransaction($merchantCode, $fromDate, $toDate, $salt, $url) 
    {

        $timestamp = Carbon::now();
        $parameter = [
            "MerchantCode" => $merchantCode,
            "FromDate" => $fromDate,
            "ToDate" => $toDate,
            "Timestamp" => $timestamp->toDateTimeString() . "T" . $timestamp->toDateTimeString(),
            "Checksum" => ""
        ];

        $md5 = md5($parameter["FromDate"] . "." . $parameter["ToDate"] . "." . $parameter["Timestamp"] . "." . $salt, true);
        $parameter["Checksum"] = base64_encode($md5);

        return LogComponent::send($url, "/api/v1/report/GetTransaction", "POST", [], $parameter);

    }


    public static function savePlayerTransaction($playerTransactions, $websiteId) 
    {

        $mytime = Carbon::now();

        if(!empty($playerTransactions)) {

            $insert = [];

            foreach($playerTransactions as $value) {

                array_push($insert, [
                    "adjustment" => [
                        "reference" => $value->adjustmentRefNo
                    ],
                    "amount" => [
                        "final" => intval($value->finalAmount),
                        "request" => intval($value->amount)
                    ],
                    "approved" => [
                        "timestamp" => $mytime->toDateTimeString(),
                        "user" => [
                            "_id" => "0",
                            "username" => $value->approvedBy
                        ]
                    ],
                    "bank" => [
                        "account" => [
                            "from" => [
                                "name" => $value->fromBankAccountName,
                                "number" => $value->fromBankAccountNo
                            ],
                            "to" => [
                                "name" => $value->toBankAccountName,
                                "number" => $value->toBankAccountNo
                            ]
                        ],
                        "from" => $value->bankFrom,
                        "to" => $value->bankTo
                    ],
                    "fee" => [
                        "admin" => intval($value->adminFee)
                    ],
                    "reference" => $value->refNo,
                    "requested" => [
                        "timestamp" => $mytime->toDateTimeString(),
                        "user" => [
                            "_id" => "0",
                            "username" => $value->requestedBy
                        ]
                    ],
                    "transaction" => [
                        "code" => $value->transactionCode,
                        "type" => $value->transactionType
                    ],
                    "username" => $value->username
                ]);

            }

            try {

                NexusPlayerTransactionModel::insertMany($insert, $websiteId);

                Log::info("Nexus player transaction inserted");

            } catch(Exception $exception) {

                if($exception->getCode() == 11000) {

                    Log::error("Nexus player transaction already exist");

                } else {

                    Log::error($exception->getMessage());

                }

            }

        }

    }


}
