<?php

namespace App\Components;

use Carbon\Carbon;


class AuthenticationComponent {


    public static function validate($request) {

        $result = DataComponent::initializeResponse("Failed to validate request");

        LogComponent::request($request);

        $currentTimestamp = Carbon::now();

        $client = DataComponent::initializeClient($request->header("nu-key"));

        if($client != null) {

            if(in_array($request->ip(), $client->ips) || in_array("*", $client->ips)) {

                openssl_private_decrypt(base64_decode($request->token), $token, $client->encryption->privateKey, OPENSSL_PKCS1_PADDING);

                $tokens = explode(",", $token);

                if(count($tokens) == 2) {

                    if($tokens[0] == $request->path()) {

                        $tokenTimestamp = Carbon::createFromFormat("Y-m-d\TH:i:sO", $tokens[1]);
                        // dd($tokens[1], $tokenTimestamp->diffInSeconds($currentTimestamp));
                        if($tokenTimestamp->diffInSeconds($currentTimestamp) <= 500000) {

                            $result->response = "Request authenticated";
                            $result->result = true;

                        } else {

                            $result->response = "Request expired";

                        }

                    } else {

                        $result->response = "Invalid path";

                    }

                } else {

                    $result->response = "Invalid token pattern";

                }

            } else {

                $result->response = "Unauthorized IP address " . $request->ip();

            }

        } else {

            $result->response = "Invalid NU key " . $request->header("nu-key");

        }

        return $result;

    }


}
