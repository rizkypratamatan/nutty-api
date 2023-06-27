<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\UserGroupModel;
use App\Services\UserGroupService;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function getUserGroup(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userGroup", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];

            $filter['name'] = !empty($request->name)?$request->name:0;
            $filter['website'] = !empty($request->website)?$request->website:0;
            $filter['status'] = !empty($request->status)?$request->status:0;
            $filter['nucode'] = !empty($request->nucode)?$request->nucode:0;

            $userModel =  new UserGroupModel();
            $data = $userModel->getAllUserGroup($limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All User Group',
                'dataUser' => $data['data'],
                'totalData' => $data['total_data']
            ];
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addUserGroup(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userGroup", "add");

            return response()->json(UserGroupService::insert($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function updateUserGroupById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userGroup", "edit");
            return response()->json(UserGroupService::update($request), 200);

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function deleteUserGroup(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "userGroup", "delete");

            return response()->json(UserGroupService::delete($request), 200);

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getUserGroupById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "userGroup", "view");

            $userModel =  new UserGroupModel();
            $user = $userModel->findOneById($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success get user group',
                    'dataUser' => $user
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get user group',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    


    public function table(Request $request) {

        if(DataComponent::checkPrivilege($request, "userGroup", "view")) {

            return response()->json(UserGroupService::findTable($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }

}
