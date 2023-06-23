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
    protected $user;
    protected $request;

    public function __construct($request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function getAllUserGroup($limit=10, $offset=0, $filter = [])
    {   
        $data = UserGroup::take($limit)->skip($offset);
        $countData = new UserGroup();

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
            $countData = $countData->where('name', 'LIKE', $filter['name']."%");
        }

        if(!empty($filter['website'])){
            $data = $data->where('websites', 'elemMatch', ["_id" => $filter['website']]);
            $countData = $countData->where('websites', 'elemMatch', ["_id" => $filter['website']]);
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

    public function addUserGroup()
    {
        $websites = [];
        if($this->request->websites){
            $arrWebsites = $this->request->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website->toArray());
                }
            }
        }

        $data = new UserGroup();
        $data->description = $this->request->description;
        $data->name = $this->request->name;
        $data->status = $this->request->status;
        $data->nucode = $this->request->nucode;
        $data->websites = $websites;
        $data->created = DataComponent::initializeTimestamp($this->user);
        $data->modified = DataComponent::initializeTimestamp($this->user);

        $data->save();

        return $data;
    }

    public function deleteUserGroup()
    {
        return UserGroup::where('_id', $this->request->id)->delete();
    }

    public function getUserGroupById()
    {

        return UserGroup::where('_id', $this->request->id)->first();
    }

    public function updateUserGroupById()
    {
        $websites = [];
        if($this->request->websites){
            $arrWebsites = $this->request->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website->toArray());
                }
            }
        }

        $data = UserGroup::find($this->request->id);
        $data->description = $this->request->description;
        $data->name = $this->request->name;
        $data->websites = $websites;
        $data->status = $this->request->status;
        $data->type = $this->request->type;
        $data->nucode = $this->request->nucode;
        $data->modified = DataComponent::initializeTimestamp($this->user);

        $data->save();

        $update = [
            "group" => [
                "_id" => DataComponent::initializeObjectId($this->request->id),
                "name" => $data->name,
            ]
        ];

        UserModel::updateByGroupId($this->request->id, $update);

        return $data;
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
