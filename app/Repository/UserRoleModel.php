<?php

namespace App\Repository;

use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRoleModel
{
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
            ],
            "status" => $data->status,
            "created" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => "0",
                    "username" => "System"
                ]
            ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => "0",
                    "username" => "System"
                ]
            ]
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

        return DB::table('userRole')
            ->where('_id', $data->id)->update($arr);
    }
}
