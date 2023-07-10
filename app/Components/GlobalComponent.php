<?php

namespace App\Components;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;


class GlobalComponent {


    public static function appName() {

        return str_replace("_", " ", config("app.name"));

    }


    public static function accountId() {

        $result = "";

        if(Session::has("account")) {

            $result = Crypt::encryptString(Session::get("account")->_id);

        }

        return $result;

    }


    public static function appPrefix() {

        return strtolower(config("app.name")) . "_";

    }


    public static function appUrl() {

        return config("app.url");

    }


}
