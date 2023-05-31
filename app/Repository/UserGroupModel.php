<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\UserGroup;
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

    public function addUserGroup($data)
    {

        $data = new UserGroup();
        $data->description = $data->description;
        $data->name = $data->name;
        $data->status = $data->status;
        $data->type = $data->type;
        $data->created = DataComponent::initializeTimestamp($this->user);
        $data->modified = DataComponent::initializeTimestamp($this->user);

        $data->save();

        return $data;
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
        $data = UserGroup::find($data->id);
        $data->description = $data->description;
        $data->name = $data->name;
        $data->status = $data->status;
        $data->type = $data->type;
        $data->modified = DataComponent::initializeTimestamp($this->user);

        $data->save();

        return $data;
    }

    public static function findByStatus($status) 
    {
        return UserGroup::where([
            ["status", "=", $status]
        ])->get();

    }
}
