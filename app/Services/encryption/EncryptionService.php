<?php

namespace App\Services\encryption;

use App\Components\DataComponent;
use Illuminate\Support\Facades\Storage;


class EncryptionService {


    public static function rsa($request) {

        $result = DataComponent::initializeResponse("Failed to RSA encryption");
        $result->decrypted = "";
        $result->encrypted = "";

        if(Storage::exists("clients/" . $request->clientKey . ".nu")) {

            $client = DataComponent::initializeClient($request->clientKey);

            if($request->decrypted != null) {

                openssl_public_encrypt($request->decrypted, $encrypted, $client->encryption->publicKey, OPENSSL_PKCS1_PADDING);
                $result->encrypted = base64_encode($encrypted);

            } else if($request->encrypted != null) {

                openssl_private_decrypt(base64_decode($request->encrypted), $decrypted, $client->encryption->privateKey, OPENSSL_PKCS1_PADDING);
                $result->decrypted = $decrypted;

            }

        }

        return $result;

    }


    public static function rsaGenerateKey($keySize) {

        $result = DataComponent::initializeResponse("Failed to generate RSA key");
        $result->privateKey = "";
        $result->publicKey = "";

        $config = [
            "config" => storage_path("app/modules/openssl.cnf"),
            "private_key_bits" => $keySize,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ];
        $keyPair = openssl_pkey_new($config);
        openssl_pkey_export($keyPair, $result->privateKey, null, $config);
        $result->publicKey = openssl_pkey_get_details($keyPair)["key"];

        return $result;

    }


}
