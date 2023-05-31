<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Repository\DatabaseAccountModel;
use App\Repository\NexusPlayerTransactionModel;


class DatabaseAccountService {


    public static function update($id, $websiteId) {

        $databaseAccountById = DatabaseAccountModel::findOneById($id, $websiteId);

        if(!empty($databaseAccountById)) {

            $nexusPlayerTransactionFirstTransaction = NexusPlayerTransactionModel::findPlayerTransaction("D", $databaseAccountById->username, $websiteId);

            if(!$nexusPlayerTransactionFirstTransaction->isEmpty()) {

                $lastIndex = count($nexusPlayerTransactionFirstTransaction) - 1;

                $databaseAccountById->deposit = [
                    "average" => [
                        "amount" => $nexusPlayerTransactionFirstTransaction->avg("amount.request")
                    ],
                    "first" => [
                        "amount" => $nexusPlayerTransactionFirstTransaction[0]->amount["request"],
                        "timestamp" => $nexusPlayerTransactionFirstTransaction[0]->requested["timestamp"]
                    ],
                    "last" => [
                        "amount" => $nexusPlayerTransactionFirstTransaction[$lastIndex]->amount["request"],
                        "timestamp" => $nexusPlayerTransactionFirstTransaction[$lastIndex]->requested["timestamp"]
                    ],
                    "total" => [
                        "amount" => $nexusPlayerTransactionFirstTransaction->sum("amount.request")
                    ]
                ];
                DatabaseAccountModel::update(DataComponent::initializeSystemAccount(), $databaseAccountById, $websiteId);

            }

        }

    }


}
