<?php

namespace App\Components;

use Illuminate\Support\Facades\Storage;


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


}
