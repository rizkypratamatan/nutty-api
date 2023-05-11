<?php

namespace App\Components;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DataComponent {


    public static function initializeClient($nukey) {

        $result = null;

        if(Storage::exists("clients/" . $nukey)) {

            $result = json_decode(Storage::get("clients/" . $nukey . "/access.nu"));
            $result->encryption->privateKey = Storage::get("clients/" . $nukey . "/private.key");
            $result->encryption->publicKey = Storage::get("clients/" . $nukey . "/public.key");

        }

        return $result;

    }


    public static function initializeResponse($response) {

        $result = new \stdClass();
        $result->response = $response;
        $result->result = false;

        return $result;

    }

    public static function checkPrivilege($request, $privilege, $action) {

        $user = AuthenticationComponent::toUser($request);
        $result = ["message" => strtoupper($user->username)." Unauthorized to ".strtoupper($action)." ".strtoupper($privilege), "code" => 403];    

        if(!empty($user)) {

            $start = 0;

            switch($action) {
                case "view":
                    $start = 0;

                    break;
                case "add":
                    $start = 1;

                    break;
                case "edit":
                    $start = 2;

                    break;
                case "delete":
                    $start = 3;

                    break;
            }

            if(substr($user->privilege[$privilege], $start, 1) == "7") {
                $result["message"] = "Authorized";
                $result["code"] = 200;
                LogComponent::response($request, $result);
            }

        }

        if($result["code"] == 403){
            LogComponent::response($request, $result);
            throw new AuthorizationException("Unauthorized", 403);
        }

    }


}
