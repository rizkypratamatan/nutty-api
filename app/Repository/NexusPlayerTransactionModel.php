<?php

namespace App\Repository;

use App\Models\NexusPlayerTransaction;


class NexusPlayerTransactionModel 
{


    public static function findPlayerTransaction($referencePrefix, $username, $websiteId) 
    {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $websiteId);

        return $nexusPlayerTransaction->where([
            ["reference", "LIKE", $referencePrefix . "%"],
            ["username", "=", $username]
        ])->orderBy("approved.timestamp", "ASC")->get(["amount.request", "requested.timestamp"]);

    }


    public static function insertMany($data, $website) 
    {

        $nexusPlayerTransaction = new NexusPlayerTransaction();
        $nexusPlayerTransaction->setTable("nexusPlayerTransaction_" . $website);
        $nexusPlayerTransaction->insert($data);

    }


}
