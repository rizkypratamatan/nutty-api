<?php

namespace App\Repository;

use App\Components\DataComponent;

class SMSModel
{
    public static function insert($data, $account){

        $data->setTable("smsLogs_".$account->_id);

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;
    }
}
