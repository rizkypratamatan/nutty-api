<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\UnclaimedDeposit;


class UnclaimedDepositModel {


    public static function deleteLteCreatedTimestamp($createdTimestamp, $websiteId) {

        $unclaimedDeposit = new UnclaimedDeposit();
        $unclaimedDeposit->setTable("unclaimedDeposit_" . $websiteId);
        $unclaimedDeposit->where([
            ["created.timestamp", "<=", $createdTimestamp]
        ])->delete();

    }


    public static function findOneById($id, $websiteId) {

        $unclaimedDeposit = new UnclaimedDeposit();
        $unclaimedDeposit->setTable("unclaimedDeposit_" . $websiteId);

        return $unclaimedDeposit->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findByStatusLimit($status, $websiteId, $limit) {

        $unclaimedDeposit = new UnclaimedDeposit();
        $unclaimedDeposit->setTable("unclaimedDeposit_" . $websiteId);

        return $unclaimedDeposit->where([
            ["status", "=", $status]
        ])->take($limit)->get();

    }


    public static function insertMany($data, $websiteId) {

        $unclaimedDeposit = new UnclaimedDeposit();
        $unclaimedDeposit->setTable("unclaimedDeposit_" . $websiteId);
        $unclaimedDeposit->insert($data);

    }


    public static function update($account, $data, $websiteId) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("unclaimedDeposit_" . $websiteId);

        return $data->save();

    }


    public static function updateByUsername($username, $data, $websiteId) {

        $unclaimedDeposit = new UnclaimedDeposit();
        $unclaimedDeposit->setTable("unclaimedDeposit_" . $websiteId);
        $unclaimedDeposit->where([
            ["username", "=", $username]
        ])->update($data, ["upsert" => false]);

    }


}
