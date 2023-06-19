<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Helpers\Authentication;
use App\Repository\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUser(Request $request)
    
    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "user", "view");

            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];
            $filter['username'] = !empty($request->username)?$request->username:"";
            $filter['name'] = !empty($request->name)?$request->name:"";
            $filter['nucode'] = !empty($request->nucode)?$request->nucode:"";
            $filter['type'] = !empty($request->type)?$request->type:"";
            $filter['group'] = !empty($request->group)?$request->group:"";
            $filter['role'] = !empty($request->role)?$request->role:"";
            $filter['status'] = !empty($request->status)?$request->status:"";

            $userModel =  new UserModel();
            $user = $userModel->getAllUser($limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All User',
                'dataUser' => $user
            ];
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addUser(Request $request)
    {
        // print_r($request->all());die();
        
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "user", "add");

            $userModel =  new UserModel();
            $user = $userModel->addUser($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success add user',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add user',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function updateUserById(Request $request)
    
    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
             //check privilege
             DataComponent::checkPrivilege($request, "user", "edit");

            $userModel =  new UserModel();
            $user = $userModel->updateUserById($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success update user',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update user',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }


    public function deleteUser(Request $request)

    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "user", "delete");

            $userModel =  new UserModel();
            $user = $userModel->deleteUser($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success delete user',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete user',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getUserById(Request $request)

    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "user", "view");

            $userModel =  new UserModel();
            $user = $userModel->getUserById($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success get user',
                    'dataUser' => $user
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get user',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

}
