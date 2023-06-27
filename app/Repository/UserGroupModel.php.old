<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\UserGroup;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserGroupModel
{
    public function getAllUserGroup($limit=10, $offset=0, $filter = [])
    {   
        $data = UserGroup::take($limit)->skip($offset);
        $response = [
            "data" => null,
            "total_data" => 0
        ];

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
        }

        if(!empty($filter['website'])){
            $data = $data->where('websites', 'elemMatch', ["_id" => $filter['website']]);
        }

        if(!empty($filter['nucode'])){
            $data = $data->where('nucode', $filter['nucode']);
        }

        if(!empty($filter['status'])){
            $data = $data->where('status', $filter['status']);
        }
        $data = $data->get();
        $total_count = $data->count();

        $response['data'] = $data;
        $response['total_data'] = $total_count;

        return $response;
    }

    public function addUserGroup($data)
    {
        $auth = AuthenticationComponent::toUser($data);
        $websites = [];
        if($data->websites){
            $arrWebsites = $data->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website->toArray());
                }
            }
        }

        $data = new UserGroup();
        $data->description = $data->description;
        $data->name = $data->name;
        $data->status = $data->status;
        $data->nucode = $data->nucode;
        $data->websites = $websites;
        $data->created = DataComponent::initializeTimestamp($auth);
        $data->modified = DataComponent::initializeTimestamp($auth);

        $data->save();

        return $data;
    }

    public function deleteUserGroup($id)
    {
        return UserGroup::where('_id', $id)->delete();
    }

    public function getUserGroupById($id)
    {
        return UserGroup::where('_id', $id)->first();
    }

    public function updateUserGroupById($data)
    {
        $auth = AuthenticationComponent::toUser($data);
        $websites = [];
        if($data->websites){
            $arrWebsites = $data->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website->toArray());
                }
            }
        }

        $data = UserGroup::find($data->id);
        $data->description = $data->description;
        $data->name = $data->name;
        $data->websites = $websites;
        $data->status = $data->status;
        $data->type = $data->type;
        $data->nucode = $data->nucode;
        $data->modified = DataComponent::initializeTimestamp($auth);

        $data->save();

        $update = [
            "group" => [
                "_id" => DataComponent::initializeObjectId($data->id),
                "name" => $data->name,
            ]
        ];

        UserModel::updateByGroupId($data->id, $update);

        return $data;
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
}
