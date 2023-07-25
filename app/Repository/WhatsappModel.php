<?php

namespace App\Repository;

use App\Components\DataComponent;

class WhatsappModel
{
    public static function insert($data, $account){

        $data->setTable("whatsappLogs_".$account->_id);

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;
        $data->save();

        return $data;
    }
}
