<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\DatabaseAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseAccountModel
{

    public static function insert($auth, $data, $websiteId) 
    {

        $mytime = Carbon::now();

        $arr = [
            "database" => [
                "_id" => $auth->_id,
                "name" => $data->name
            ],
            "deposit" => [
                "average" => [
                    "amount" => "0",
                ],
                "first" => [
                    "amount" => "0",
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "last" => [
                    "amount" => "0",
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "total" => [
                    "amount" => "0"
                ]
            ],
            "games" => [],
            "login" => [
                "average" => [
                    "daily" => 0,
                    "monthly" => 0,
                    "weekly" => 0,
                    "yearly" => 0
                ],
                "first" => [
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "last" => [
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "total" => [
                    "amount" => "0"
                ]
            ],
            "reference" => "",
            "register" => [
                "timestamp" => $mytime->toDateTimeString()
            ],
            "username" => $data->username,
            "withdrawal" => [
                "average" => [
                    "amount" => "0"
                ],
                "first" => [
                    "amount" => "0",
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "last" => [
                    "amount" => "0",
                    "timestamp" => $mytime->toDateTimeString()
                ],
                "total" => [
                    "amount" => "0"
                ]
            ],
            "created" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $data->username
                ]
            ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $data->username
                ]
            ]
        ];

        return DB::table('databaseAccount')
            ->insert($arr);

    }

    public static function count($websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([])->count("_id");

    }


    public static function delete($id) 
    {

        return DatabaseAccount::where('_id', $id)->delete();
    }


    public static function deleteByDatabaseId($databaseId, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([
            ["database._id", "=", $databaseId]
        ])->delete();

    }


    public static function findAll($websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([])->get();

    }


    public static function findOneByDatabaseId($databaseId, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([
            ["database._id", "=", $databaseId]
        ])->first();

    }


    public static function findOneById($id, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByUsername($username, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([
            ["username", "=", $username]
        ])->first();

    }


    public static function findOneByUsernameNotDatabaseId($databaseId, $username, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([
            ["database._id", "!=", $databaseId],
            ["username", "=", $username]
        ])->first();

    }


    public static function findPageSort($page, $size, $sort, $websiteId) 
    {

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);

        return $databaseAccount->where([])->orderBy($sort["field"], $sort["direction"])->forPage($page, $size)->get();

    }


    


    public static function update($account, $data, $websiteId) 
    {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("databaseAccount_" . $websiteId);

        return $data->save();

    }


    public static function updateByDatabaseId($account, $databaseId, $data, $websiteId) 
    {

        $data["modified"] = DataComponent::initializeTimestamp($account);

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);
        $databaseAccount->where("database._id", $databaseId)->update($data, ["upsert" => false]);

    }



    
}
