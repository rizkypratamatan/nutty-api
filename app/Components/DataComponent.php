<?php

namespace App\Components;

use App\Models\User;
use App\Repository\UserModel;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class DataComponent {


    public static function initializeClient($nukey) {

        $result = null;

        if(Storage::exists("clients/" . $nukey)) {

            $result = json_decode(Storage::get("clients/" . $nukey . "/access.nu"));
            $result->encryption->privateKey = Storage::get("clients/" . $nukey . "/private.key");
            $result->encryption->publicKey = Storage::get("clients/" . $nukey . "/public.key");

        }

        return $result;

    }


    public static function initializeResponse($response) {

        $result = new \stdClass();
        $result->response = $response;
        $result->result = false;

        return $result;

    }

    public static function checkPrivilege($request, $privilege, $action) {

        $user = AuthenticationComponent::toUser($request);
        $result = ["message" => strtoupper($user->username)." Unauthorized to ".strtoupper($action)." ".strtoupper($privilege), "code" => 403];    

        if(!empty($user)) {

            $start = 0;

            switch($action) {
                case "view":
                    $start = 0;

                    break;
                case "add":
                    $start = 1;

                    break;
                case "edit":
                    $start = 2;

                    break;
                case "delete":
                    $start = 3;

                    break;
            }

            if(substr($user->privilege[$privilege], $start, 1) == "7") {
                $result["message"] = strtoupper($user->username)." Authorized to ".strtoupper($action)." ".strtoupper($privilege);
                $result["code"] = 200;
                LogComponent::response($request, $result);
            }

        }

        if($result["code"] == 403){
            LogComponent::response($request, $result);
            throw new AuthorizationException("Unauthorized", 403);
        }
    }

    public static function initializeCollectionByWebsite($websiteId) 
    {

        if(!Schema::hasTable("database_" . $websiteId)) {

            Schema::create("database_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseIndex($table);

            });

        } else {

            Schema::table("database_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseIndex($table);

            });

        }
        
        if(!Schema::hasTable("databaseAccount_" . $websiteId)) {

            Schema::create("databaseAccount_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseAccountIndex($table);

            });

        } else {

            Schema::table("databaseAccount_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseAccountIndex($table);

            });

        }

        if(!Schema::hasTable("databaseLog_" . $websiteId)) {

            Schema::create("databaseLog_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseLogIndex($table);

            });

        } else {

            Schema::table("databaseLog_" . $websiteId, function(Blueprint $table) {

                self::createDatabaseLogIndex($table);

            });

        }

        if(!Schema::hasTable("nexusPlayerTransaction_" . $websiteId)) {

            Schema::create("nexusPlayerTransaction_" . $websiteId, function(Blueprint $table) {

                self::createNexusPlayerTransactionIndex($table);

            });

        } else {

            Schema::table("nexusPlayerTransaction_" . $websiteId, function(Blueprint $table) {

                self::createNexusPlayerTransactionIndex($table);

            });

        }

    }

    public static function initializeSystemAccount() 
    {

        $result = new UserModel();
        $result->_id = "0";
        $result->avatar = "";
        $result->group["_id"] = "0";
        $result->group["name"] = "System";
        $result->name = "System";
        $result->username = "System";

        return $result;

    }

    public static function initializeTimestamp($account) 
    {
        $mytime = Carbon::now();

        return [
            "timestamp" => $mytime->toDateTimeString(),
            "user" => [
                "_id" => self::initializeObjectId($account->_id),
                "avatar" => $account->avatar,
                "name" => $account->name,
                "username" => $account->username
            ]
        ];

    }

    public static function initializeObjectId($id) 
    {

        $result = "0";

        if($id != "0") {

            $result = new User($id);

        }

        return $result;

    }

    public static function initializeAccount($request) 
    {

        $result = new User();

        if($request->session()->has("account")) {

            $result = $request->session()->get("account");

        }

        return $result;

    }


    public static function createDatabaseIndex($table) {

        $table->string("city")->index();
        $table->string("contact.email")->index();
        $table->string("contact.line")->index();
        $table->string("contact.michat")->index();
        $table->string("contact.phone")->unique();
        $table->string("contact.telegram")->index();
        $table->string("contact.wechat")->index();
        $table->string("contact.whatsapp")->index();
        $table->string("country")->index();
        $table->string("crm._id")->index();
        $table->string("crm.avatar")->index();
        $table->string("crm.name")->index();
        $table->string("crm.username")->index();
        $table->string("gender")->index();
        $table->string("group._id")->index();
        $table->string("group.name")->index();
        $table->string("import._id")->index();
        $table->string("import.file")->index();
        $table->string("language")->index();
        $table->string("name")->index();
        $table->string("reference")->index();
        $table->string("state")->index();
        $table->string("status")->index();
        $table->string("street")->index();
        $table->string("telemarketer._id")->index();
        $table->string("telemarketer.avatar")->index();
        $table->string("telemarketer.name")->index();
        $table->string("telemarketer.username")->index();
        $table->string("zip")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();
    }

    public static function createDatabaseAccountIndex($table) {

        $table->string("database._id")->unique();
        $table->string("database.name")->index();
        $table->string("deposit.average.amount")->index();
        $table->string("deposit.first.amount")->index();
        $table->date("deposit.first.timestamp")->index();
        $table->string("deposit.last.amount")->index();
        $table->date("deposit.last.timestamp")->index();
        $table->string("deposit.total.amount")->index();
        $table->integer("login.average.daily")->index();
        $table->integer("login.average.monthly")->index();
        $table->integer("login.average.weekly")->index();
        $table->integer("login.average.yearly")->index();
        $table->date("login.first.timestamp")->index();
        $table->date("login.last.timestamp")->index();
        $table->string("login.total.amount")->index();
        $table->string("reference")->index();
        $table->date("register.timestamp")->index();
        $table->string("username")->unique();
        $table->string("withdrawal.average.amount")->index();
        $table->string("withdrawal.first.amount")->index();
        $table->date("withdrawal.first.timestamp")->index();
        $table->string("withdrawal.last.amount")->index();
        $table->date("withdrawal.last.timestamp")->index();
        $table->string("withdrawal.total.amount")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }

    public static function createDatabaseLogIndex($table) {

        $table->string("database._id")->index();
        $table->string("database.name")->index();
        $table->string("reference")->index();
        $table->string("status")->index();
        $table->string("user._id")->index();
        $table->string("user.avatar")->index();
        $table->string("user.name")->index();
        $table->string("user.username")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }

    public static function createNexusPlayerTransactionIndex($table) {

        $table->string("adjustment.reference")->index();
        $table->decimal("amount.final")->index();
        $table->decimal("amount.request")->index();
        $table->date("approved.timestamp")->index();
        $table->string("approved.user._id")->index();
        $table->string("approved.user.username")->index();
        $table->string("bank.account.from.name")->index();
        $table->string("bank.account.from.number")->index();
        $table->string("bank.account.to.name")->index();
        $table->string("bank.account.to.number")->index();
        $table->string("bank.from")->index();
        $table->string("bank.to")->index();
        $table->decimal("fee.admin")->index();
        $table->string("reference")->unique();
        $table->date("requested.timestamp")->index();
        $table->string("requested.user._id")->index();
        $table->string("requested.user.username")->index();
        $table->string("transaction.code")->index();
        $table->string("transaction.type")->index();
        $table->string("username")->index();

    }

    public static function initializeTimestamp($account) {

        $mytime = Carbon::now();
        return [
            "timestamp" => $mytime->toDateTimeString(),
            "user" => [
                "_id" => self::initializeObjectId($account->_id),
                "avatar" => $account->avatar,
                "name" => $account->name,
                "username" => $account->username
            ]
        ];

    }

    public static function initializeObjectId($id) {

        $result = "0";

        if($id != "0") {

            $result = new ObjectId($id);

        }

        return $result;

    }

    public static function initializeSystemAccount() {

        $result = new stdClass();
        $result->_id = "0";
        $result->avatar = "";
        $result->group["_id"] = "0";
        $result->group["name"] = "System";
        $result->name = "System";
        $result->username = "System";

        return $result;

    }
}
