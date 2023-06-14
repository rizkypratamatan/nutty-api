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

    public function getRole()
    {
        return UserRole::get();
    }

    public function addRole($data)
    {

        $mytime = Carbon::now();

        $arr = [
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "privilege" => [
                "database" => $data->privilege->database,
                "report" => $data->privilege->report,
                "setting" => $data->privilege->setting,
                "settingApi" => $data->privilege->settingApi,
                "user" => $data->privilege->user,
                "userGroup" => $data->privilege->userGroup,
                "userRole" => $data->privilege->userRole,
                "website" => $data->privilege->website,
                "worksheet" => $data->privilege->worksheet,
                "whatsapp" => $data->privilege->whatsapp,
                "sms" =>  $data->privilege->sms,
                "email" =>  $data->privilege->email
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
        $mytime = Carbon::now();

        $arr = [
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "privilege" => [
                "database" => $data->privilege->database,
                "report" => $data->privilege->report,
                "setting" => $data->privilege->setting,
                "settingApi" => $data->privilege->settingApi,
                "user" => $data->privilege->user,
                "userGroup" => $data->privilege->userGroup,
                "userRole" => $data->privilege->userRole,
                "website" => $data->privilege->website,
                "worksheet" => $data->privilege->worksheet,
                "whatsapp" => $data->privilege->whatsapp,
                "sms" =>  $data->privilege->sms,
                "email" =>  $data->privilege->email
            ],
            "status" => $data->status,
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => "0",
                    "username" => "System"
                ]
            ]
        ];

        $update = [
            "privilege" => $arr['privilege'],
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
