<?php

namespace App\Http\Controllers;

use App\Helpers\Authentication;
use App\Http\Requests\Login\LoginRequest;
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
        $checkToken = Authentication::validate($request);        
        if ($checkToken->original->result) {
            $userModel =  new UserModel;
            $user = $userModel->getUserByUsername($request->username);            
            echo json_encode($checkToken); die();
            $pass =  Crypt::decryptString($user['password']['main']);
            
            if (Authentication::DecryptionPassword($request) == $pass) {
                $data = [
                    'id' => (string)$user['_id'],
                    'name' => $user['name'],
                    'role' => $user['role']['name'],
                    'type' => $user['type'],
                ];

                $userLog = new UserLogModel();
                $userLog->insertToLog($pass);

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
            $response = $checkToken->original;
        }
        return response()->json($response, 200);
    }
}
