<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Services\UserService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request)
    {   
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            return UserService::register($request);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }
}
