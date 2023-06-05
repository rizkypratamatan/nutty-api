<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\UserGroupModel;
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
            $userModel =  new UserGroupModel($request);
            $user = $userModel->getAllUserGroup($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All User Group',
                'dataUser' => $user
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

            $userModel =  new UserGroupModel($request);
            $user = $userModel->addUserGroup();

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success add user group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add user group',
                ];
            }
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

            $userModel =  new UserGroupModel($request);
            $user = $userModel->updateUserGroupById();

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success update user group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update user group',
                ];
            }
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

            $userModel =  new UserGroupModel($request);
            $user = $userModel->deleteUserGroup();

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success delete user group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete user group',
                ];
            }
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

            $userModel =  new UserGroupModel($request);
            $user = $userModel->getUserGroupById();

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
}
