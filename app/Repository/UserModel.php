<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class UserModel
{
    public static function countByNucode($nucode) {

        return User::where([
            ["nucode", "=", $nucode]
        ])->count();

    }


    public static function delete($data) {

        return $data->delete();

    }


    public static function deleteByNucode($nucode) {

        return User::where("nucode", $nucode)->delete();

    }


    public static function findByNucode($nucode) {

        return User::where([
            ["nucode", "=", $nucode]
        ])->get();

    }


    public static function findOneByContactEmail($contactEmail) {

        return User::where([
            ["contact.email", "=", $contactEmail]
        ])->first();

    }


    public static function findOneById($id) {

        return User::where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByNucodeUsername($nucode, $username) {

        return User::where([
            ["nucode", "=", $nucode],
            ["username", "=", $username]
        ])->first();

    }


    public static function findOneByUsername($username) {

        return User::where([
            ["username", "=", $username]
        ])->first();

    }

    public function getUserByUsername($username)
    {
        return User::where('username', $username)
            ->first();
    }
    
    public function getAllUser($limit, $offset, $filter = [])
    {
        //$username, $name, $nucode, $type, $group, $role, $status
        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $users = User::take($limit)->skip($offset);

        if(!empty($filter['username'])){
            $users = $users->where('username', $filter['username']);
        }

        if(!empty($filter['name'])){
            $users = $users->where('name', 'LIKE', $filter['name'].'%');
        }

        if(!empty($filter['nucode'])){
            $users = $users->where('nucode', $filter['nucode']);
        }

        if(!empty($filter['type'])){
            $users = $users->where('type', $filter['type']);
        }

        if(!empty($filter['group'])){
            $users = $users->where('group._id', $filter['group']);
        }

        if(!empty($filter['role'])){
            $users = $users->where('role._id', $filter['role']);
        }

        if(!empty($filter['status'])){
            $users = $users->where('status', $filter['status']);
        }

        $users = $users->where('username', '<>', 'system')->get();

        $response = [
            "data" => $users,
            "total_data" => $users->count()
        ];
        

        return $response;
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
            'email' => $data->contact['email'],
            'fax' => $data->contact['fax'],
            'line' => $data->contact['line'],
            'michat' => $data->contact['michat'],
            'phone' => $data->contact['phone'],
            'wechat' => $data->contact['wechat'],
            'whatsapp' => $data->contact['whatsapp'],
            'telegram' => $data->contact['telegram'],
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
            'email' => $data->contact['email'],
            'fax' => $data->contact['fax'],
            'line' => $data->contact['line'],
            'michat' => $data->contact['michat'],
            'phone' => $data->contact['phone'],
            'wechat' => $data->contact['wechat'],
            'whatsapp' => $data->contact['whatsapp'],
            'telegram' => $data->contact['telegram'],
        ];
        if($data->password){
            $user->password = [
                'main' => Crypt::encryptString($data->password),
                'recovery' => Crypt::encryptString($data->password)
            ];
        }
        
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
