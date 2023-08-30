<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Helpers\Authentication;
use App\Repository\UserModel;
use App\Services\UserService;
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

            $account = DataComponent::initializeAccount($request);

            $userModel =  new UserModel();
            $user = $userModel->getAllUser($account->nucode, $limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All User',
                'dataUser' => $user['data'],
                'total_data' => $user['total_data']
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
            return response()->json(UserService::insert($request), 200);

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
             return response()->json(UserService::update($request), 200);
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
            return response()->json(UserService::delete($request), 200);

            // $userModel =  new UserModel();
            // $user = $userModel->deleteUser($request->id);

            // if ($user) {
            //     $response = [
            //         'result' => true,
            //         'response' => 'success delete user',
            //     ];
            // } else {
            //     $response = [
            //         'result' => false,
            //         'response' => 'failed delete user',
            //     ];
            // }
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

    public function initializeData(Request $request)

    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "user", "view");
            return response()->json(UserService::initializeData($request), 200);

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

}
