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

    public function getRole($nucode, $limit, $offset, $filter)
    {
        if($nucode == 'system'){
            $data = UserRole::take($limit)->skip($offset);
            $countData = new UserRole();
        }else{
            $data = UserRole::take($limit)->skip($offset)->where('nucode', $nucode);
            $countData = UserRole::where('nucode', $nucode);
        }

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

    public function addRole($request)
    {
        $account = AuthenticationComponent::toUser($request);

        if($account->nucode == "system"){
            $nucode = $request->nucode;
        }else{
            $nucode = $account->nucode;
        }

        $data = new UserRole();
        $data->description = $request->description;
        $data->name = $request->name;
        $data->nucode = $nucode;
        $data->privilege = [
            "database" => $request->privilege['database'],
            "report" => $request->privilege['report'],
            "setting" => $request->privilege['setting'],
            "settingApi" => $request->privilege['settingApi'],
            "user" => $request->privilege['user'],
            "userGroup" => $request->privilege['userGroup'],
            "userRole" => $request->privilege['userRole'],
            "website" => $request->privilege['website'],
            "worksheet" => $request->privilege['worksheet'],
            "worksheetCrm" => $request->privilege['worksheetCrm'],
            "tools" => $request->privilege['tools'],
        ];
        $data->status = $request->status;
        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = DataComponent::initializeTimestamp($account);
        $data->save();
        // $arr = [
        //     "description" => $data->description,
        //     "name" => $data->name,
        //     "nucode" => $data->nucode,
        //     "privilege" => [
        //         "database" => $data->privilege['database'],
        //         "report" => $data->privilege['report'],
        //         "setting" => $data->privilege['setting'],
        //         "settingApi" => $data->privilege['settingApi'],
        //         "user" => $data->privilege['user'],
        //         "userGroup" => $data->privilege['userGroup'],
        //         "userRole" => $data->privilege['userRole'],
        //         "website" => $data->privilege['website'],
        //         "worksheet" => $data->privilege['worksheet'],
        //         "worksheetCrm" => $data->privilege['worksheetCrm'],
        //         "tools" => $data->privilege['tools'],
        //         // "whatsapp" => $data->privilege['whatsapp'],
        //         // "sms" =>  $data->privilege['sms'],
        //         // "email" =>  $data->privilege['email']
        //     ],
        //     "status" => $data->status,
        //     "created" => DataComponent::initializeTimestamp($this->user),
        //     "modified" => DataComponent::initializeTimestamp($this->user)
        // ];

        return $data;
    }

    public static function deleteRole($id)
    {

        return UserRole::where('_id', $id)->delete();
    }

    public function getRoleById($id)
    {

        return UserRole::where('_id', $id)->first();
    }

    public function updateRoleById($request)
    {
        $account = AuthenticationComponent::toUser($request);

        $data = UserRole::find($request->id);
        $data->description = $request->description;
        $data->name = $request->name;
        $data->nucode = $request->nucode;
        $data->privilege = [
            "database" => $request->privilege['database'],
            "report" => $request->privilege['report'],
            "setting" => $request->privilege['setting'],
            "settingApi" => $request->privilege['settingApi'],
            "user" => $request->privilege['user'],
            "userGroup" => $request->privilege['userGroup'],
            "userRole" => $request->privilege['userRole'],
            "website" => $request->privilege['website'],
            "worksheet" => $request->privilege['worksheet'],
            "worksheetCrm" => $request->privilege['worksheetCrm'],
            "tools" => $request->privilege['tools'],
        ];
        $data->status = $request->status;
        $data->modified = DataComponent::initializeTimestamp($account);
        $data->save();
        // $arr = [
        //     "description" => $data->description,
        //     "name" => $data->name,
        //     "nucode" => $data->nucode,
        //     "privilege" => [
        //         "database" => $data->privilege['database'],
        //         "report" => $data->privilege['report'],
        //         "setting" => $data->privilege['setting'],
        //         "settingApi" => $data->privilege['settingApi'],
        //         "user" => $data->privilege['user'],
        //         "userGroup" => $data->privilege['userGroup'],
        //         "userRole" => $data->privilege['userRole'],
        //         "website" => $data->privilege['website'],
        //         "worksheet" => $data->privilege['worksheet'],
        //         "worksheetCrm" => $data->privilege['worksheetCrm'],
        //         "tools" => $data->privilege['tools']
        //         // "whatsapp" => $data->privilege['whatsapp'],
        //         // "sms" =>  $data->privilege['sms'],
        //         // "email" =>  $data->privilege['email']
        //     ],
        //     "status" => $data->status,
        //     "modified" => DataComponent::initializeTimestamp($this->user)
        // ];

        $update = [
            "privilege" => $data->privilege,
            "role" => [
                "_id" => DataComponent::initializeObjectId($data->id),
                "name" => $data->name
            ]
        ];

        UserModel::updateByRoleId($data->id, $update);

        return $data;
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

    public static function delete($data) {

        return $data->delete();

    }


    public static function deleteByNucode($nucode) {

        return UserRole::where("nucode", $nucode)->delete();

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
