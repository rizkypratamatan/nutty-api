<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserModel
{

    public function __construct()
    {   
    }

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
        $user = new User();
        $user->name = $data->name;
        $user->gender = $data->gender;
        $user->contact = [
            'email' => $data->email,
            'fax' => $data->fax,
            'line' => $data->line,
            'michat' => $data->michat,
            'phone' => $data->phone,
            'wechat' => $data->wechat,
            'whatsapp' => $data->whatsapp,
            'telegram' => $data->telegram,
        ];
        $user->password = [
            'main' => Crypt::encryptString($data->password),
            'recovery' => Crypt::encryptString($data->password)
        ];
        $user->role = [
            '_id' => $data->role['_id'],
            'name' => $data->role['name']
        ];
        $user->group = [
            '_id' => $data->group['_id'],
            'name' => $data->group['name']
        ];
        $user->username = $data->username;
        $user->country = $data->country;
        $user->city = $data->city;
        $user->street = $data->street;
        $user->zip = $data->zip;
        $user->created = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));
        $user->modified = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));
        $user->save();

        DataComponent::initializeCollectionByAccount($user->_id);

        return $user;
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
        $user = User::find($data->id);
        $user->name = $data->name;
        $user->gender = $data->gender;
        $user->contact = [
            'email' => $data->email,
            'fax' => $data->fax,
            'line' => $data->line,
            'michat' => $data->michat,
            'phone' => $data->phone,
            'wechat' => $data->wechat,
            'whatsapp' => $data->whatsapp,
            'telegram' => $data->telegram,
        ];
        $user->password = [
            'main' => Crypt::encryptString($data->password),
            'recovery' => Crypt::encryptString($data->password)
        ];
        $user->role = [
            '_id' => $data->role['_id'],
            'name' => $data->role['name']
        ];
        $user->group = [
            '_id' => $data->group['_id'],
            'name' => $data->group['name']
        ];
        $user->username = $data->username;
        $user->country = $data->country;
        $user->city = $data->city;
        $user->street = $data->street;
        $user->zip = $data->zip;
        $user->modified = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));
        $user->save();

        return $user;
    }

    public static function findOneByIdStatus($id, $status) {

        return User::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();

    }
}
