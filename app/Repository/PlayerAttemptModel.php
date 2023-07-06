<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\PlayerAttempt;

class PlayerAttemptModel 
{

    public static function findOneById($id, $nucode) {

        $playerAttempt = new PlayerAttempt();
        $playerAttempt->setTable("playerAttempt_" . $nucode);

        return $playerAttempt->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByUsername($nucode, $username) {

        $playerAttempt = new PlayerAttempt();
        $playerAttempt->setTable("playerAttempt_" . $nucode);

        return $playerAttempt->where([
            ["username", "=", $username]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("playerAttempt_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("playerAttempt_" . $account->nucode);

        return $data->save();

    }


}
