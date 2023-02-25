<?php

namespace App\Components;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class LogComponent {


    private static function initializeData($data) {

        if(is_bool($data)) {

            if($data) {

                $data = "true";

            } else {

                $data = "false";

            }

        } else if(is_array($data) || is_object($data)) {

            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

        }

        return $data;

    }


    private static function initializeRequest($request) {

        $result = "";

        $header = "Headers: ";

        foreach($request->header() as $key => $requestHeaders) {

            $item = "";

            foreach($requestHeaders as $requestHeader) {

                $item .= $requestHeader . ", ";

            }

            $header .= $key . "=" . substr($item, 0, strlen($item) - 2) . ", ";

        }

        $result .= substr($header, 0, strlen($header) - 2) . "\n";

        $data = "Data: ";

        foreach($request->all() as $key => $requestData) {

            $data .= $key . "=" . self::initializeData($requestData) . ", ";

        }

        $result .= substr($data, 0, strlen($data) - 2);

        return $result;

    }


    private static function initializeResponse($data) {

        $result = "Data: ";

        foreach($data as $key => $resultData) {

            $result .= $key . "=" . self::initializeData($resultData) . ", ";

        }

        $result = substr($result, 0, strlen($result) - 2);

        return $result;

    }


    public static function request($request) {

        Log::info("[" . Carbon::now()->format("Y-m-d H:i:s") . "] Request to " . $request->path() . "\n" . self::initializeRequest($request));

    }


    public static function response($request, $result) {

        Log::info("[" . Carbon::now()->format("Y-m-d H:i:s") . "] Response from " . $request->path() . "\n" . self::initializeResponse($result));

    }


}
