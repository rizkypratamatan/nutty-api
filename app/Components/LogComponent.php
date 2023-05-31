<?php

namespace App\Components;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;


class LogComponent {


    private static function initializeData($data)
    {

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


    private static function initializeRequest($request) 
    {

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


    private static function initializeResponse($data) 
    {

        $result = "Data: ";

        foreach($data as $key => $resultData) {

            $result .= $key . "=" . self::initializeData($resultData) . ", ";

        }

        $result = substr($result, 0, strlen($result) - 2);

        return $result;

    }


    public static function request($request) 
    {

        Log::info("[" . Carbon::now()->format("Y-m-d H:i:s") . "] Request to " . $request->path() . "\n" . self::initializeRequest($request));

    }


    public static function response($request, $result) 
    {

        Log::info("[" . Carbon::now()->format("Y-m-d H:i:s") . "] Response from " . $request->path() . "\n" . self::initializeResponse($result));

    }

    public static function send($baseUrl, $path, $method, $header, $parameter) 
    {

        $result = new \stdClass();
        $result->content = new \stdClass();
        $result->response = "Failed to send REST API";
        $result->result = false;
        $result->status = 0;

        Log::info("Request to " . $baseUrl . $path . " with parameter : " . self::generateParameter($parameter));

        $curl = curl_init();

        if(strtoupper($method) == "POST") {

            curl_setopt($curl, CURLOPT_POST, 1);

        } else if(strtoupper($method) == "PUT") {

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

        } else if(strtoupper($method) == "DELETE") {

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

        }

        if(!empty($header)) {

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        }

        if(!empty($parameter)) {

            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parameter));

        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl, CURLOPT_URL, $baseUrl . $path);

        try {

            $response = curl_exec($curl);
            $result->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($result->status == 200) {

                $result->content = json_decode($response);

                $result->response = "REST API sent";
                $result->result = true;

            } else {

                Log::error(curl_error($curl));

            }

            curl_close($curl);

        } catch(Exception $exception) {

            Log::error($exception->getMessage());

        }

        Log::info("Response from " . $baseUrl . $path . " with return : " . self::generateParameter($result));

        return $result;

    }

    private static function generateParameter($parameter) 
    {

        $result = "";

        if($parameter != null) {

            foreach($parameter as $key => $value) {

                if(is_bool($value)) {

                    if($value) {

                        $value = "true";

                    } else {

                        $value = "false";

                    }

                } else if(is_array($value) || is_object($value)) {

                    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

                }

                $result .= ", " . $key . " = " . strval($value);

            }

            $result = preg_replace("|, |", "", $result, 1);

        }

        return $result;

    }


}
