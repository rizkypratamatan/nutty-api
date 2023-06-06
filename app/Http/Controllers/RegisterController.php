<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\RegisterModel;
use App\Repository\UserModel;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function addRegister(Request $request)
    {
        // print_r($request->all());die();
        
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "user", "add");

            $userModel =  new RegisterModel($request);
            $user = $userModel->addRegister($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success register',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed register',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }
}
