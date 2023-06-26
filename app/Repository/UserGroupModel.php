<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\UserGroup;


class UserGroupModel {

    public function getAllUserGroup($limit=10, $offset=0, $filter = [])
    {   
        $data = UserGroup::take($limit)->skip($offset);
        $countData = new UserGroup();

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
            $countData = $countData->where('name', 'LIKE', $filter['name']."%");
        }

        if(!empty($filter['website'])){
            $data = $data->where('website.ids', 'all', [$filter['website']]);
        }

        if(!empty($filter['nucode'])){
            $data = $data->where('nucode', $filter['nucode']);
            $countData = $countData->where('nucode', $filter['nucode']);
        }

        if(!empty($filter['status'])){
            $data = $data->where('status', $filter['status']);
            $countData = $countData->where('status', $filter['status']);
        }
        $data = $data->get();
        $counData = $countData->count();

        $response['data'] = $data;
        $response['total_data'] = $counData;

        return $response;
    }

    public static function delete($data) {

        return $data->delete();

    }


    public static function deleteByNucode($nucode) {

        return UserGroup::where("nucode", $nucode)->delete();

    }


    public static function findByNucodeStatus($nucode, $status) {

        return UserGroup::where([
            ["nucode", "=", $nucode],
            ["status", "=", $status]
        ])->get();

    }


    public static function findByStatus($status) {

        return UserGroup::where([
            ["status", "=", $status]
        ])->get();

    }


    public static function findOneById($id) {

        return UserGroup::where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByIdStatus($id, $status) {

        return UserGroup::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();

    }


    public static function findOneByNameNucode($name, $nucode) {

        return UserGroup::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();

    }


    public static function findOneByWebsiteIdsNotWebsiteNames($websiteId, $websiteName) {

        return UserGroup::where([
            ["website.ids", "=", $websiteId],
            ["website.names", "!=", $websiteName]
        ])->first();

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
