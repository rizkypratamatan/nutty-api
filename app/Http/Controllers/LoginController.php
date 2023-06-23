<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Helpers\Authentication;
use App\Http\Requests\Login\LoginRequest;
use App\Repository\UserGroupModel;
use App\Repository\UserLogModel;
use App\Repository\UserModel;
use App\Services\encryption\EncryptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function userLogin(LoginRequest $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {
            
            $userModel =  new UserModel();
            $user = $userModel->getUserByUsername($request->username);            
            
            $pass =  Crypt::decryptString($user['password']['main']);
            $groupModel = new UserGroupModel();
            if ($request->password == $pass) {
                $data = [
                    'id' => (string)$user['_id'],
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'group' => $user['group'],
                    'website' => $user['group'],
                    'privilege' => $user['privilege'],
                    'type' => $user['type'],
                ];

                if((!empty($user['group']) and strtolower($user['group']['name']) != "system")){
                    $data['group'] = $groupModel->getUserGroupById($user['group']['_id']);
                }

                $userLog = new UserLogModel();
                $tokenAuth = $userLog->insertToLog($user, "Login", null);

                $data["token-auth"] = $tokenAuth;

                $response = [
                    'result' => 'true',
                    'response' => 'Login successful',
                    'dataUser' => $data
                ];
            } else {
                $response = [
                    'result' => 'false',
                    'response' => 'Login failed'
                ];
            }
        } else {
            $response = $validation;
        }
        
        return response()->json($response, 200);
    }

    public function userLogout(Request $request)
    {
        
        $authentication = !empty($request->header('token-auth'))?$request->header('token-auth'):null;
        $userLogByAuthenticationInType = UserLogModel::findOneByAuthentication($authentication);

        if(!empty($userLogByAuthenticationInType)) {
            $type = "Logout";
            $userModel =  new UserModel;
            $user = $userModel->getUserByUsername($userLogByAuthenticationInType->user['username']);
            $userLog = new UserLogModel();
            $tokenAuth = $userLog->insertToLog($user, $type, $authentication);    
        }

        $response = [
            "result" => true
        ];

        return response()->json($response, 200);
    }
}
