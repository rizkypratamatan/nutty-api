<?php

namespace App\Http\Controllers;

use App\Services\encryption\EncryptionService;
use Illuminate\Http\Request;


class EncryptionController {


    public function rsa(Request $request) {

        $encryption = EncryptionService::rsa($request);

        $data = [
            "clientKey" => $request->clientKey,
            "decrypted" => $encryption->decrypted,
            "encrypted" => $encryption->encrypted
        ];

        return view("encryption/encryption")->with("data", $data);

    }


    public function rsaGenerateKey(Request $request, $keySize) {

        $keyPair = EncryptionService::rsaGenerateKey($keySize);

        $data = [
            "privateKey" => $keyPair->privateKey,
            "publicKey" => $keyPair->publicKey
        ];

        return view("encryption/generate-key")->with("data", $data);

    }


}
