<?php

namespace App\Repository;

use App\Models\Database;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseModel
{
    public function getDatabase($limit=10, $offset=0)
    {   
        return Database::get()->take($limit)->skip($offset);
    }

    public function addDatabase($data, $auth)
    {

        $mytime = Carbon::now();

        $arr = [
            "city" => $data->city,
            "contact" => [
                "email" => $data->email,
                "line" => $data->line,
                "michat" => $data->michat,
                "phone" => $data->phone,
                "telegram" => $data->telegram,
                "wechat" => $data->wechat,
                "whatsapp" => $data->whatsapp
            ],
            "country" => $data->country,
            "crm" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => ""
            ],
            "gender" => $data->gender,
            "group" => [
                "_id" => $auth->_id,
                "name" => $auth->username
            ],
            "import" => [
                "_id" => "",
                "file" => ""
            ],
            "language" => $data->language,
            "name" => $data->name,
            "reference" => $data->reference,
            "state" => $data->state,
            "status" => $data->status,
            "street" => $data->street,
            "telemarketer" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => ""
            ],
            "zip" => $data->zip,
            "created" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ]
        ];

        // $websiteByNameNucode = self::findOneByNameNucode($data->name, $data->nucode);

        // if(!empty($websiteByNameNucode)) {

        //     if(!$data->id == $websiteByNameNucode->id) {

        //         // array_push($validation, false);

        //         // $data->response = "Website name already exist";

        //         return false;

        //     }

        // }

        return DB::table('database')
            ->insert($arr);
    }

    public static function deleteDatabase($id)
    {

        return Database::where('_id', $id)->delete();
    }

    public function getDatabaseById($id)
    {

        return Database::where('_id', $id)->first();
    }

    public function updateDatabaseById($data, $auth)
    {
        $mytime = Carbon::now();

        $arr = [
            "city" => $data->city,
            "contact" => [
                "email" => $data->email,
                "line" => $data->line,
                "michat" => $data->michat,
                "phone" => $data->phone,
                "telegram" => $data->telegram,
                "wechat" => $data->wechat,
                "whatsapp" => $data->whatsapp
            ],
            "country" => $data->country,
            // "crm" => [
            //     "_id" => "",
            //     "avatar" => "",
            //     "name" => "",
            //     "username" => ""
            // ],
            "gender" => $data->gender,
            // "group" => [
            //     "_id" => $auth->_id,
            //     "name" => $auth->username
            // ],
            "import" => [
                "_id" => "",
                "file" => ""
            ],
            "language" => $data->language,
            "name" => $data->name,
            "reference" => $data->reference,
            "state" => $data->state,
            "status" => $data->status,
            "street" => $data->street,
            "telemarketer" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => ""
            ],
            "zip" => $data->zip,
            // "created" => [
            //     "timestamp" => $mytime->toDateTimeString(),
            //     "user" => [
            //         "_id" => $auth->_id,
            //         "username" => $auth->username
            //     ]
            // ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ]
        ];

        return DB::table('website')->where('_id', $data->id)->update($arr);
    }

    public static function findOneById($id) 
    {

        return Database::where([
            ["_id", "=", $id]
        ])->first();

    }

    public static function findOneByNameNucode($name, $nucode) 
    {

        return Database::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();

    }


    // public static function count() {

    //     return Database::where([])->count("_id");

    // }


    // public static function delete($data) {

    //     return $data->delete();

    // }


    // public static function deleteByNucode($nucode) {

    //     return Database::where("nucode", $nucode)->delete();

    // }


    // public static function findAll() {

    //     return Database::where([])->get();

    // }


    // public static function findByNucode($nucode) {

    //     return Database::where([
    //         ["nucode", "=", $nucode]
    //     ])->get();

    // }


    // public static function findByNucodeStatus($nucode, $status) {

    //     return Database::where([
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findByStatus($status) {

    //     return Database::where([
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findInId($ids) {

    //     return Database::whereIn("_id", $ids)->get();

    // }


    // public static function findByStatusNotApiNexusSaltStart($apiNexusSalt, $start, $status) {

    //     return Database::where([
    //         ["api.nexus.salt", "!=", $apiNexusSalt],
    //         ["start", "!=", $start],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    


    // public static function findOneByIdNucodeStatus($id, $nucode, $status) {

    //     return Database::where([
    //         ["_id", "=", $id],
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->first();

    // }


    


    // public static function findPageNotApiNexusSaltStart($apiNexusSalt, $start, $page, $size) {

    //     return Database::where([
    //         ["api.nexus.salt", "!=", $apiNexusSalt],
    //         ["start", "!=", $start]
    //     ])->forPage($page, $size)->get();

    // }


    // public static function insert($account, $data) {

    //     $data->created = DataComponent::initializeTimestamp($account);
    //     $data->modified = $data->created;

    //     $data->save();

    //     return $data;

    // }


    // public static function update($account, $data) {

    //     if($account != null) {

    //         $data->modified = DataComponent::initializeTimestamp($account);

    //     }

    //     return $data->save();

    // }
}
