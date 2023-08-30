<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\NexusPlayerTransaction;


class NexusPlayerTransactionModel {


    public static function countByTransactionTypeLikeUsername($transactionType, $username, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);

        return $nexusPlayerTransaction->where([
            ["transaction.type", "=", $transactionType],
            ["username", "LIKE", $username]
        ])->count("_id");

    }


    public static function deleteLteApprovedTimestamp($approvedTimestamp, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);
        $nexusPlayerTransaction->where([
            ["approved.timestamp", "<=", $approvedTimestamp]
        ])->delete();

    }


    public static function findOneByReference($reference, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);

        return $nexusPlayerTransaction->where([
            ["reference", "=", $reference]
        ])->first();

    }


    public static function findPlayerTransaction($referencePrefix, $username, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);

        return $nexusPlayerTransaction->where([
            ["reference", "LIKE", $referencePrefix . "%"],
            ["username", "=", $username]
        ])->orderBy("approved.timestamp", "ASC")->get(["amount.request", "requested.timestamp"]);

    }


    public static function findPendingPlayerTransaction($createdTimestamp, $referencePrefix, $username, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);

        return $nexusPlayerTransaction->where([
            ["created.timestamp", ">", $createdTimestamp],
            ["reference", "LIKE", $referencePrefix . "%"],
            ["username", "LIKE", $username]
        ])->whereNull("claim")->orderBy("approved.timestamp", "ASC")->get([
            "amount.request",
            "requested.timestamp",
            "created.timestamp"
        ]);

    }


    public static function insertMany($data, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);
        $nexusPlayerTransaction->insert($data);

    }


    public static function update($account, $data, $websiteId) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("nexusPlayerTransaction_" . $websiteId);

        return $data->save();

    }


    public static function updateClaimById($claim, $id, $websiteId) {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);
        $nexusPlayerTransaction->where("_id", $id)->update(["claim" => $claim], ["upsert" => false]);

    }


}
