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

    public function getAllUserGroup($limit=10, $offset=0)
    {   
        return UserGroup::get()->take($limit)->skip($offset);
    }

    public function addUserGroup()
    {
        $websites = [];
        if($this->request->websites){
            $arrWebsites = $this->request->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website);
                }
            }
        }

        $data = new UserGroup();
        $data->description = $this->request->description;
        $data->name = $this->request->name;
        $data->status = $this->request->status;
        $data->websites = $websites;
        $data->type = $this->request->type;
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

        return UserGroup::where('_id', $this->request->id)->get();
    }

    public function updateUserGroupById()
    {
        $websites = [];
        if($this->request->websites){
            $arrWebsites = $this->request->websites;

            foreach($arrWebsites as $value){
                $website = Website::where("_id", $value)->first();
                
                if($website){
                    array_push($websites, $website);
                }
            }
        }

        $data = UserGroup::find($this->request->id);
        $data->description = $this->request->description;
        $data->name = $this->request->name;
        $data->websites = $websites;
        $data->status = $this->request->status;
        $data->type = $this->request->type;
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
