<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\UserRoleModel;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getRole(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userRole", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $userModel =  new UserRoleModel();
            $user = $userModel->getRole($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All User Role',
                'dataUser' => $user
            ];
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addRole(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userRole", "add");

            $userModel =  new UserRoleModel();
            $user = $userModel->addRole($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success add user role',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add user role',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function updateRoleById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "userRole", "edit");

            $userModel =  new UserRoleModel();
            $user = $userModel->updateRoleById($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success update role',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update role',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function deleteRole(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "userRole", "delete");

            $userModel =  new UserRoleModel();
            $user = $userModel->deleteRole($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success delete role',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete role',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getRoleById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "userRole", "view");

            $userModel =  new UserRoleModel();
            $user = $userModel->getRoleById($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success get role',
                    'dataUser' => $user
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get role',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
