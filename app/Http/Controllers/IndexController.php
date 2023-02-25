<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use Illuminate\Http\Request;


class IndexController extends Controller {


    public function index(Request $request) {

        $validation = AuthenticationComponent::validate($request);

        LogComponent::response($request, $validation);

        return response()->json($validation, 200);

    }


}
