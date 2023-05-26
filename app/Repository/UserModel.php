<?php

namespace App\Repository;

use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserModel
{

    public function getUserByUsername($username)
    {
        return User::where('username', $username)
            ->first();
    }
    
    public function getAllUser($limit=10, $offset=0)
    {
        return User::get()->take($limit)->skip($offset);
    }

    public function addUser($data)
    {

        $mytime = Carbon::now();
        $arr = [
            'name' => $data->name,
            'gender' => $data->gender,
            'contact' => [
                'email' => $data->email,
                'fax' => $data->fax,
                'line' => $data->line,
                'michat' => $data->michat,
                'phone' => $data->phone,
                'wechat' => $data->wechat,
                'whatsapp' => $data->whatsapp,
                'telegram' => $data->telegram,
            ],
            'password' => [
                'main' => Crypt::encryptString($data->password),
                'recovery' => Crypt::encryptString($data->password)
            ],
            'username' => $data->username,
            'country' => $data->country,
            'city' => $data->city,
            'street' => $data->street,
            'zip' => $data->zip,
            'created' => [
                'timestamp' => $mytime->toDateTimeString()
            ]
        ];

        return DB::table('user')
            ->insert($arr);
    }

    public function deleteUser($id)
    {

        return User::where('_id', $id)->delete();
    }

    public function getUserById($id)
    {

        return User::where('_id', $id)->first();
    }

    public function updateUserById($data)
    {
        $mytime = Carbon::now();

        $arr = [
            'name' => $data->name,
            'gender' => $data->gender,
            'contact' => [
                'email' => $data->email,
                'fax' => $data->fax,
                'line' => $data->line,
                'michat' => $data->michat,
                'phone' => $data->phone,
                'wechat' => $data->wechat,
                'whatsapp' => $data->whatsapp,
                'telegram' => $data->telegram,
            ],
            'password' => [
                'main' => Crypt::encryptString($data->password),
                'recovery' => Crypt::encryptString($data->password)
            ],
            'username' => $data->username,
            'country' => $data->country,
            'city' => $data->city,
            'street' => $data->street,
            'zip' => $data->zip,
            'modified' => [
                'timestamp' => $mytime->toDateTimeString()
            ]
        ];

        return DB::table('user')
            ->where('_id', $data->id)->update($arr);
    }

    public static function findOneByIdStatus($id, $status) {

        return User::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();

    }
}
