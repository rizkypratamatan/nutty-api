<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Database;
use App\Models\DatabaseAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseAccountModel
{
    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function insert($auth, $data, $websiteId) 
    {

        $mytime = Carbon::now();

        $arr = [
            "database" => [
                "_id" => $this->user->_id,
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
            "created" => DataComponent::initializeTimestamp($this->user),
            "modified" => DataComponent::initializeTimestamp($this->user)
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


    


    public function update($auth, $data, $websiteId) 

    {
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
                "modified" => DataComponent::initializeTimestamp($this->user)
            ];
    
            return DB::table('databaseAccount')
            ->where('_id', $data->id)->update($arr);
    
        }

    }


    public static function updateByDatabaseId($account, $databaseId, $data, $websiteId) 
    {

        $data["modified"] = DataComponent::initializeTimestamp($account);

        $databaseAccount = new DatabaseAccount();
        $databaseAccount->setTable("databaseAccount_" . $websiteId);
        $databaseAccount->where("database._id", $databaseId)->update($data, ["upsert" => false]);

    }



    
}
