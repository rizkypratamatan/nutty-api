<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\Setting;

class SettingModel
{
    // protected $service;
    // protected $user;
    // protected $request;

    public function getAll()
    {
        $data = new Setting();

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $counData = $data->count();
        if($counData > 0){
            $data = $data->first();
        }else{
            $data = "";
        }

        $response['data'] = $data;
        $response['total_data'] = $counData;

        return $response;
    }

    public static function insert($account, $data)
    {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;
    }

    public static function update($account, $data)
    {

        if ($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);
        }

        return $data->save();
    }
}
