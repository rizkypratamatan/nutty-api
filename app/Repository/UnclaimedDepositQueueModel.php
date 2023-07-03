<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\UnclaimedDepositQueue;


class UnclaimedDepositQueueModel {


    public static function delete($data) {

        return $data->delete();

    }


    public static function findOneByStatus($status) {

        return UnclaimedDepositQueue::where([
            ["status", "=", $status]
        ])->orderBy("created.timestamp", "ASC")->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        return $data->save();

    }


}
