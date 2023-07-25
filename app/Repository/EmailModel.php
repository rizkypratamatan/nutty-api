<?php

namespace App\Repository;

use App\Components\DataComponent;

class EmailModel
{
    public static function insert($data, $account){

        $data->setTable("emailLogs_".$account->_id);

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;
    }
}
