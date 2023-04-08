<?php

namespace App\Components;

use Illuminate\Support\Facades\Storage;


class DataComponent {


    public static function initializeClient($nukey) {

        $result = null;

        if(Storage::exists("clients/" . $nukey . ".nu")) {

            $result = json_decode(Storage::get("clients/" . $nukey . ".nu"));

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
