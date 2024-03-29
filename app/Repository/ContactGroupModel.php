<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use Illuminate\Support\Facades\DB;

class ContactGroupModel
{
    public static function getAll($limit=10, $offset=0, $auth)
    {   
        return DB::table("contact_groups_".$auth->_id)
                            ->take($limit)
                            ->skip($offset)
                            ->get();
    }

    public static function add($data, $account)
    {

        $arr = [
            "name" => $data->name,
            "created" => DataComponent::initializeTimestamp($account),
            "modified" => DataComponent::initializeTimestamp($account)
        ];

        return DB::table("contact_groups_".$account->_id)->insert($arr);
    }

    public static function delete($id, $account)
    {
        DB::table("contact_".$account->_id)
            ->pull("groups", ['_id' => $id]);

        return DB::table("contact_groups_".$account->_id)->where("_id", $id)->delete();
    }

    public static function getById($id, $account)
    {

        return DB::table("contact_groups_".$account->_id)
                    ->where("_id", $id)
                    ->first();
    }

    public static function updateById($data, $account)
    {
        $arr = [
            "name" => $data->name,
            "modified" => DataComponent::initializeTimestamp($account)
        ];

        DB::table("contact_groups_".$account->_id)
                    ->where("_id", $data->id)
                    ->update($arr);

        $update = DB::table("contact_groups_".$account->_id)
                    ->where("_id", $data->id)->first();

        //update group in contact
        DB::table("contact_".$account->_id)
            ->pull("groups", ['_id' => $data->id]);

        DB::table("contact_".$account->_id)
            ->push("groups", $update);


        return $update;
    }

}
