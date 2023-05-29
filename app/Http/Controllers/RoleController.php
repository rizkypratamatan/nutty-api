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

            $model =  new UserRoleModel();
            $data = $model->getRole($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All User Role',
                'data' => $data
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

            $model =  new UserRoleModel();
            $data = $model->addRole($request);

            if ($data) {
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

            $model =  new UserRoleModel();
            $data = $model->updateRoleById($request);

            if ($data) {
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

            $model =  new UserRoleModel();
            $data = $model->deleteRole($request->id);

            if ($data) {
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

            $model =  new UserRoleModel();
            $data = $model->getRoleById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get role',
                    'data' => $data
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
