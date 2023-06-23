<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleModel
{
    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function getRole($limit, $offset, $filter)
    {
        $data = UserRole::take($limit)->skip($offset);
        $countData = new UserRole();

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
            $countData = $countData->where('name', 'LIKE', $filter['name']."%");
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

    public function addRole($data)
    {
        $arr = [
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "privileges" => [
                "database" => $data->privileges['database'],
                "report" => $data->privileges['report'],
                "setting" => $data->privileges['setting'],
                "settingApi" => $data->privileges['settingApi'],
                "user" => $data->privileges['user'],
                "userGroup" => $data->privileges['userGroup'],
                "userRole" => $data->privileges['userRole'],
                "website" => $data->privileges['website'],
                "worksheet" => $data->privileges['worksheet'],
                "whatsapp" => $data->privileges['whatsapp'],
                "sms" =>  $data->privileges['sms'],
                "email" =>  $data->privileges['email']
            ],
            "status" => $data->status,
            "created" => DataComponent::initializeTimestamp($this->user),
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('userRole')
            ->insert($arr);
    }

    public static function deleteRole($id)
    {

        return UserRole::where('_id', $id)->delete();
    }

    public function getRoleById($id)
    {

        return UserRole::where('_id', $id)->first();
    }

    public function updateRoleById($data)
    {
        $arr = [
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "privileges" => [
                "database" => $data->privileges['database'],
                "report" => $data->privileges['report'],
                "setting" => $data->privileges['setting'],
                "settingApi" => $data->privileges['settingApi'],
                "user" => $data->privileges['user'],
                "userGroup" => $data->privileges['userGroup'],
                "userRole" => $data->privileges['userRole'],
                "website" => $data->privileges['website'],
                "worksheet" => $data->privileges['worksheet'],
                "whatsapp" => $data->privileges['whatsapp'],
                "sms" =>  $data->privileges['sms'],
                "email" =>  $data->privileges['email']
            ],
            "status" => $data->status,
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        $update = [
            "privileges" => $arr['privileges'],
            "role" => [
                "_id" => DataComponent::initializeObjectId($data->id),
                "name" => $arr['name']
            ]
        ];

        UserModel::updateByRoleId($data->id, $update);

        return DB::table('userRole')
            ->where('_id', $data->id)->update($arr);
    }

    public static function findByNucodeStatus($nucode, $status) {

        return UserRole::where([
            ["nucode", "=", $nucode],
            ["status", "=", $status]
        ])->get();

    }


    public static function findByStatus($status) {
        return UserRole::where([
            ["status", "=", $status]
        ])->get();
    }


    public static function findOneById($id) {
        return UserRole::where([
            ["_id", "=", $id]
        ])->first();
    }


    public static function findOneByIdStatus($id, $status) {
        return UserRole::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();
    }


    public static function findOneByNameNucode($name, $nucode) {
        return UserRole::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();
    }
}
