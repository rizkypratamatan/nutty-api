<?php

namespace App\Http\Controllers;

use App\Helpers\Authentication;
use App\Repository\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $checkToken = Authentication::validate($request); 
        if ($checkToken->original->result) {
            $userModel =  new UserModel();
            $user = $userModel->getAllUser();

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

    public function addUser(Request $request)
    {
        $checkToken = Authentication::validate($request); 

        if ($checkToken->original->result) {
            $userModel =  new UserModel();
            $user = $userModel->addUser($request);

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
    public function updateUserById(Request $request)
    {
        $checkToken = Authentication::validate($request); 

        if ($checkToken->original->result) {
            $userModel =  new UserModel();
            $user = $userModel->updateUserById($request);

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

    public function deleteUser(Request $request)
    {
        $checkToken = Authentication::validate($request); 
        if ($checkToken->original->result) {
            $userModel =  new UserModel();
            $user = $userModel->deleteUser($request->id);

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

    public function getUserById(Request $request)
    {
        $checkToken = Authentication::validate($request); 
        if ($checkToken->original->result) {
            $userModel =  new UserModel();
            $user = $userModel->getUserById($request->id);

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
