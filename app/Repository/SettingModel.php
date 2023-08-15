<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingModel
{
    // protected $service;
    // protected $user;
    // protected $request;

    public function getAll($nucode)
    {
        $data = new Setting();
        $data->setTable("settings_".$nucode);

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $counData = $data->count();
        if($counData > 0){
            $data = $data->get();
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

    public static function getSettingByName($name, $nucode)
    {
        return DB::table("settings_".$nucode)->where('name', $name)->first();
    }

    public static function getSettingByNucode($nucode)
    {
        return DB::table("settings_".$nucode)->get();
    }
}
