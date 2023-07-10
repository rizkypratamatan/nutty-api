<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Components\GlobalComponent;
use App\Models\UserLog;
use App\Repositories\UserLogRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use stdClass;


class UserLogService {


    public static function insert($userAgent, $authentication, $description, $log, $targetId, $targetName, $user) {

        if($authentication == null) {

            $authentication = Hash::make($user->nucode . $user->_id . Carbon::now()->format("Y-m-dTH:i:s.u"));

        }

        $userLog = new UserLog();
        $userLog->authentication = $authentication;
        $userLog->agent = $userAgent;
        $userLog->description = $description;
        $userLog->nucode = $user->nucode;
        $userLog->target = [
            "_id" => $targetId,
            "name" => $targetName
        ];
        $userLog->type = $log;
        $userLog->user = [
            "_id" => $user->_id,
            "username" => $user->username
        ];

        return UserLogRepository::insert($user, $userLog);

    }


    public static function login($request) {

        $result = new stdClass();
        $result->response = "Failed to login user";
        $result->result = false;

        $userAgent = DataComponent::initializeUserAgent($request);

        $user = UserRepository::findOneByUsername($request->username);

        if(config("app.nucode") == "PUBLIC" && strtolower($request->username) != "system") {
            if(is_null($request->nucode)) {
                $result->response = "Please fill in company.";

                return $result;
            }
        }

        if(!is_null($request->nucode)) {

            $user = UserRepository::findOneByNucodeUsername($request->nucode, $request->username);

        }

        if(!empty($user)) {

            $user->password = [
                "main" => Crypt::decryptString($user->password["main"]),
                "recovery" => Crypt::decryptString($user->password["recovery"])
            ];

            if($request->password == $user->password["main"] || $request->password == $user->password["recovery"]) {

                $authentication = "";
                $lastLog = "Logout";

                $types = [
                    "Login",
                    "Logout"
                ];
                $userLogByUserIdInType = UserLogRepository::findOneByUserIdInType($user->_id, $types);

                if(!empty($userLogByUserIdInType)) {

                    $authentication = $userLogByUserIdInType->authentication;
                    $lastLog = $userLogByUserIdInType->type;

                }

                if($lastLog == "Login") {

                    self::insert($userAgent, $authentication, "Logout", "Logout", $user->_id, $user->username, $user);

                }

                $userLogLast = self::insert($userAgent, $authentication, "Login", "Login", $user->_id, $user->username, $user);

                Cookie::queue(cookie(GlobalComponent::appPrefix() . "sid", $userLogLast->authentication, 60 * 24 * 90));

                $result->response = "User logged in";
                $result->result = true;

            } else {

                $result->response = "Invalid password";

            }

        } else {

            $result->response = "Invalid username";

        }

        return $result;

    }


    public static function logout($request) {

        $result = new stdClass();
        $result->response = "Failed to logout user";
        $result->result = false;

        if($request->hasCookie(GlobalComponent::appPrefix() . "sid")) {

            if($request->session()->has("account")) {

                $userLogsByAuthentication = UserLogRepository::findOneByAuthentication($request->cookie(GlobalComponent::appPrefix() . "sid"));

                if(!empty($userLogsByAuthentication)) {

                    self::insert($userLogsByAuthentication->agent, $request->cookie(GlobalComponent::appPrefix() . "sid"), "Logout", "Logout", $request->session()->get("account")->_id, $request->session()->get("account")->username, $request->session()->get("account"));

                    $result->response = "User logged out";
                    $result->result = true;

                } else {

                    $result->response = "User log data doesn't exist";

                }

                $request->session()->forget("account");
                $request->session()->forget("websiteId");
                $request->session()->flush();

                Cookie::queue(Cookie::forget(GlobalComponent::appPrefix() . "sid"));

            } else {

                $result->response = "Session expired";

            }

        } else {

            $result->response = "Session expired";

        }

        return $result;

    }


}
