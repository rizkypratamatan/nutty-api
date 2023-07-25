<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\DatabaseAttempt;


class DatabaseAttemptModel {


    public static function findOneByContactPhone($contactPhone, $nucode) {

        $databaseAttempt = new DatabaseAttempt();
        $databaseAttempt->setTable("databaseAttempt_" . $nucode);

        return $databaseAttempt->where([
            ["contact.phone", "=", $contactPhone]
        ])->first();

    }


    public static function findOneById($id, $nucode) {

        $databaseAttempt = new DatabaseAttempt();
        $databaseAttempt->setTable("databaseAttempt_" . $nucode);

        return $databaseAttempt->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("databaseAttempt_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function insertMany($data, $nucode) {

        $databaseAttempt = new DatabaseAttempt();
        $databaseAttempt->setTable("databaseAttempt_" . $nucode);
        $databaseAttempt->insert($data);

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("databaseAttempt_" . $account->nucode);

        return $data->save();

    }


}
