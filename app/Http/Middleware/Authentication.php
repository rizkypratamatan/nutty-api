<?php

namespace App\Http\Middleware;

use App\Components\GlobalComponent;
use App\Repositories\UserLogRepository;
use App\Repositories\UserRepository;
use App\Repository\UserLogModel;
use App\Repository\UserModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;


class Authentication {


    public function handle(Request $request, Closure $next) {
        
        $authentication = !empty($request->header('token-auth'))?$request->header('token-auth'):null;
        
        if ($authentication == null) {
            
            return response([
                "status" => 403,
                "message" => "Unauthenticated",
                "data" => $authentication
            ], 403);
        }
        
        //validate token auth
        $types = [
            "Login",
            "Logout"
        ];

        $userLogByAuthenticationInType = UserLogModel::findOneByAuthenticationInType($authentication, $types);
        
        if(!empty($userLogByAuthenticationInType)) {

            if($userLogByAuthenticationInType->type == "Login") {

                $userByIdStatus = UserModel::findOneByIdStatus($userLogByAuthenticationInType->user["_id"], "Active");
                
                if(empty($userByIdStatus)) {
                    return response([
                        "status" => 401,
                        "message" => "Unauthorized"
                    ], 401);
                }

            } else {
                
                return response([
                    "status" => 403,
                    "message" => "Unauthenticated"
                ], 403);

            }

        }else{
            return response([
                "status" => 403,
                "message" => "Unauthenticated"
            ], 403);
        }

        return $next($request);

    }


}
