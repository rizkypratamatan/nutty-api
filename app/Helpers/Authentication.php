<?php

namespace App\Helpers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Services\encryption\EncryptionService;

class Authentication
{

    public static function validate($request)
    {
        $validation = AuthenticationComponent::validate($request);

        LogComponent::response($request, $validation);

        return response()->json($validation, 200);
    }

    public static function DecryptionPassword($request, $pass = ''){

        if($pass == '')
            $pass = $request->password;

        $data = (object) [
            "clientKey" => $request->header("nu-key"),
            "encrypted" => $pass,
            "decrypted" => null,
        ];

        $encryption = EncryptionService::rsa($data);

        return $encryption->decrypted;
    }
}
