<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

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
        $user->type = $data->type;
        $user->nucode = $data->nucode;
        $user->status = $data->status;
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
        $user->privilege = [
            "database" => "0000",
            "report" => "0000",
            "setting" => "0000",
            "settingApi" => "0000",
            "user" => "0000",
            "userGroup" => "0000",
            "userRole" => "0000",
            "website" => "0000",
            "worksheet" => "0000",
            "whatsapp" => "0000",
            "sms" =>  "0000",
            "email" =>  "0000"
        ];
        $user->created = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));
        $user->modified = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));

        //user role priviledge
        $userRoleByIdStatus = UserRoleModel::findOneByIdStatus($user->role["_id"], "Active");
        if(!empty($userRoleByIdStatus)) {

            $user->privilege = $userRoleByIdStatus->privilege;
            $user->role = [
                "_id" => DataComponent::initializeObjectId($userRoleByIdStatus->_id),
                "name" => $userRoleByIdStatus->name
            ];

        }

        //user group
        $userGroupByIdStatus = UserGroupModel::findOneByIdStatus($user->group["_id"], "Active");
        if(!empty($userGroupByIdStatus)) {

            $user->group = [
                "_id" => DataComponent::initializeObjectId($userGroupByIdStatus->_id),
                "name" => $userGroupByIdStatus->name
            ];

        }

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
        $user = User::findOrFail($data->id);
        $user->name = $data->name;
        $user->gender = $data->gender;
        $user->type = $data->type;
        $user->nucode = $data->nucode;
        $user->status = $data->status;
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
        $user->privilege = [
            "database" => "0000",
            "report" => "0000",
            "setting" => "0000",
            "settingApi" => "0000",
            "user" => "0000",
            "userGroup" => "0000",
            "userRole" => "0000",
            "website" => "0000",
            "worksheet" => "0000",
            "whatsapp" => "0000",
            "sms" =>  "0000",
            "email" =>  "0000"
        ];
        $user->username = $data->username;
        $user->country = $data->country;
        $user->city = $data->city;
        $user->street = $data->street;
        $user->zip = $data->zip;
        $user->modified = DataComponent::initializeTimestamp(AuthenticationComponent::toUser($data));

        //user role priviledge
        $userRoleByIdStatus = UserRoleModel::findOneByIdStatus($user->role["_id"], "Active");
        if(!empty($userRoleByIdStatus)) {

            $user->privilege = $userRoleByIdStatus->privilege;
            $user->role = [
                "_id" => DataComponent::initializeObjectId($userRoleByIdStatus->_id),
                "name" => $userRoleByIdStatus->name
            ];

        }

        //user group
        $userGroupByIdStatus = UserGroupModel::findOneByIdStatus($user->group["_id"], "Active");
        if(!empty($userGroupByIdStatus)) {

            $user->group = [
                "_id" => DataComponent::initializeObjectId($userGroupByIdStatus->_id),
                "name" => $userGroupByIdStatus->name
            ];

        }

        $user->save();

        return $user;
    }

    public static function findOneByIdStatus($id, $status) {

        return User::where([
            ["_id", "=", $id],
            ["status", "=", $status]
        ])->first();

    }

    public static function updateByGroupId($groupId, $data) {

        User::where("group._id", $groupId)->update($data, ["upsert" => false]);

    }


    public static function updateByRoleId($roleId, $data) {

        User::where("role._id", $roleId)->update($data, ["upsert" => false]);

    }
}
