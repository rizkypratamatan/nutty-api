<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getUserRole(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($checkToken->original->result) {
            $userModel =  new UserGroupModel();
            $user = $userModel->getAllUserGroup();

            $response = [
                'result' => true,
                'response' => 'Get All User Group',
                'dataUser' => $user
            ];
        } else {
            $response = $checkToken->original;
        }

        return response()->json($response, 200);
    }

    public function addUserRole(Request $request)
    {
        $checkToken = Authentication::validate($request);

        if ($checkToken->original->result) {
            $userModel =  new UserGroupModel();
            $user = $userModel->addUserGroup($request);

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
            $response = $checkToken->original;
        }

        return response()->json($response, 200);
    
    }
    public function updateUserRoleById(Request $request)
    {
        $checkToken = Authentication::validate($request);

        if ($checkToken->original->result) {
            $userModel =  new UserGroupModel();
            $user = $userModel->updateUserGroupById($request);

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
            $response = $checkToken->original;
        }

        return response()->json($response, 200);
    }

    public function deleteUserRole(Request $request)
    {
        $checkToken = Authentication::validate($request);
        
        if ($checkToken->original->result) {
            $userModel =  new UserGroupModel();
            $user = $userModel->deleteUserGroup($request->id);

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
            $response = $checkToken->original;
        }

        return response()->json($response, 200);
    }

    public function getUserRoleById(Request $request)
    {
        $checkToken = Authentication::validate($request);
        if ($checkToken->original->result) {
            $userModel =  new UserGroupModel();
            $user = $userModel->getUserGroupById($request->id);

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
            $response = $checkToken->original;
        }

        return response()->json($response, 200);
    }
}
