<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Components\RestComponent;
use App\Repository\DatabaseAccountModel;
use App\Repository\NexusPlayerTransactionModel;
use App\Repository\UnclaimedDepositModel;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;


class ApiNexusService {


    public static function findPlayerTransaction($merchantCode, $fromDate, $toDate, $salt, $url) {

        $timestamp = Carbon::now();
        $parameter = [
            "MerchantCode" => $merchantCode,
            "FromDate" => $fromDate,
            "ToDate" => $toDate,
            "Timestamp" => $timestamp->format("Y-m-d") . "T" . $timestamp->format("H:i:s"),
            "Checksum" => ""
        ];

        $md5 = md5($parameter["FromDate"] . "." . $parameter["ToDate"] . "." . $parameter["Timestamp"] . "." . $salt, true);
        $parameter["Checksum"] = base64_encode($md5);

        return RestComponent::send($url, "/api/v1/report/GetTransaction", "POST", [], $parameter);

    }


    public static function savePlayerTransaction($playerTransactions, $unclaimed, $websiteId) {

        if(!empty($playerTransactions)) {

            $insert = [];
            $insertUnclaimed = [];
            $usernames = [];

            foreach($playerTransactions as $value) {

                $timestamp = new UTCDateTime(Carbon::now());

                array_push($insert, [
                    "adjustment" => [
                        "reference" => $value->adjustmentRefNo
                    ],
                    "amount" => [
                        "final" => floatval($value->finalAmount),
                        "request" => floatval($value->amount)
                    ],
                    "approved" => [
                        "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", str_replace("T", " ", $value->approvedDate))),
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
                        "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", str_replace("T", " ", $value->requestedDate))),
                        "user" => [
                            "_id" => "0",
                            "username" => $value->requestedBy
                        ]
                    ],
                    "transaction" => [
                        "code" => $value->transactionCode,
                        "type" => $value->transactionType
                    ],
                    "user" => [
                        "_id" => "0",
                        "username" => "System"
                    ],
                    "username" => $value->username,
                    "created" => [
                        "timestamp" => $timestamp,
                        "user" => [
                            "_id" => "0",
                            "username" => "System"
                        ]
                    ],
                    "modified" => [
                        "timestamp" => $timestamp,
                        "user" => [
                            "_id" => "0",
                            "username" => "System"
                        ]
                    ]
                ]);

                if($unclaimed) {

                    if($value->transactionType == "Deposit" && !in_array(strtolower($value->username), $usernames)) {

                        $countDeposit = NexusPlayerTransactionModel::countByTransactionTypeLikeUsername("Deposit", $value->username, $websiteId);
                        $type = "FirstDeposit";

                        if($countDeposit > 0) {

                            $type = "Redeposit";

                        }

                        array_push($insertUnclaimed, [
                            "amount" => [
                                "final" => floatval($value->finalAmount),
                                "request" => floatval($value->amount)
                            ],
                            "date" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", str_replace("T", " ", $value->approvedDate))),
                            "reference" => $value->refNo,
                            "status" => true,
                            "type" => $type,
                            "username" => strtolower($value->username),
                            "created" => [
                                "timestamp" => $timestamp,
                                "user" => [
                                    "_id" => "0",
                                    "username" => "System"
                                ]
                            ],
                            "modified" => [
                                "timestamp" => $timestamp,
                                "user" => [
                                    "_id" => "0",
                                    "username" => "System"
                                ]
                            ]
                        ]);

                        array_push($usernames, strtolower($value->username));

                    }

                }

                $databaseAccountByUsername = DatabaseAccountModel::findOneByUsername(strtolower($value->username), $websiteId);

                if(!empty($databaseAccountByUsername)) {

                    $databaseAccountByUsername->deposit = [
                        "average" => $databaseAccountByUsername->deposit["average"],
                        "first" => $databaseAccountByUsername->deposit["first"],
                        "last" => [
                            "amount" => $value->amount,
                            "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", str_replace("T", " ", $value->approvedDate)))
                        ],
                        "total" => [
                            "amount" => floatval($databaseAccountByUsername->deposit["total"]["amount"]) + floatval($value->amount),
                            "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", str_replace("T", " ", $value->approvedDate)))
                        ]
                    ];
                    DatabaseAccountModel::update(DataComponent::initializeSystemAccount(), $databaseAccountByUsername, $websiteId);

                }

                usleep(1000);

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

            if($unclaimed) {

                try {

                    UnclaimedDepositModel::insertMany($insertUnclaimed, $websiteId);

                    Log::info("Unclaimed deposit inserted");

                } catch(Exception $exception) {

                    if($exception->getCode() == 11000) {

                        Log::error("Unclaimed deposit already exist");

                    } else {

                        Log::error($exception->getMessage());

                    }

                }

            }

        }

    }



}
