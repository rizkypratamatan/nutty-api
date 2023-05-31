<?php

namespace App\Repository;

use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserGroupModel
{
    public function getAllUserGroup($limit=10, $offset=0)
    {   
        return UserGroup::get()->take($limit)->skip($offset);
    }

    public function addUserGroup($data)
    {

        $mytime = Carbon::now();

        $arr = [
            'description' => $data->description,
            'name' => $data->name,
            'status' => $data->status,
            'type' => $data->type,
            'created' => [
                'timestamp' => $mytime->toDateTimeString()
            ]
        ];

        return DB::table('userGroup')
            ->insert($arr);
    }

    public static function deleteUserGroup($id)
    {

        return UserGroup::where('_id', $id)->delete();
    }

    public function getUserGroupById($id)
    {

        return UserGroup::where('_id', $id)->first();
    }

    public function updateUserGroupById($data)
    {
        $mytime = Carbon::now();

        $arr = [
            'description' => $data->description,
            'name' => $data->name,
            'status' => $data->status,
            'type' => $data->type,
            'modified' => [
                'timestamp' => $mytime->toDateTimeString()
            ]
        ];

        return DB::table('userGroup')
            ->where('_id', $data->id)->update($arr);
    }

    public static function findByStatus($status) 
    {
        return UserGroup::where([
            ["status", "=", $status]
        ])->get();

    }

    public static function findOneByIdStatus($id, $status) {

        return UserGroup::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();

    }
}
