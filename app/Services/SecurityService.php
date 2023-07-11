<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use stdClass;


class SecurityService {


    public static function encrypt($request) {

        $result = new stdClass();
        $result->response = "Failed to encrypt string";
        $result->result = false;
        $result->string = "";

        if($request->action == "Encrypt") {

            $result->string = Crypt::encryptString($request->string);

        } else if($request->action == "Decrypt") {

            $result->string = Crypt::decryptString($request->string);

        }

        $result->response = "String encrypted";
        $result->result = true;

        return $result;

    }


    public static function initializeAccount($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize account";
        $result->result = false;

        if($request->session()->has("account")) {

            $result->account = $request->session()->get("account");

        }

        $result->response = "Account initialized";
        $result->result = true;

        return $result;

    }


}
