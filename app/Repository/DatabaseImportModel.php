<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\DatabaseImport;
use App\Services\DatabaseImportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseImportModel
{
    // public function getDatabase($limit=10, $offset=0)
    // {   
    //     return Database::get()->take($limit)->skip($offset);
    // }

    public static function historyDelete($id)
    {

        return DatabaseImportService::historyDelete('_id', $id)->delete();
    }

    public function importDatabase($data, $auth)
    {

        $mytime = Carbon::now();

        $arr = [
            "file" => $data->file,
            "group" => [
                "_id" => $auth->_id,
                "name" => $auth->name
            ],
            "row" => 0,
            "status" => $data->status,
            // "website" => [
            //     "_id" => "",
            //     "name" => ""
            // ],
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


        return DB::table('databaseImport')
            ->insert($arr);
    }

    public static function initializeData($id) 
    {

        return DatabaseImport::where([
            ["_id", "=", $id]
        ])->first();

    }

    public static function findOneById($id, $nucode) 
    {

        $databaseImport = new DatabaseImport();
        $databaseImport->setTable("databaseImport_" . $nucode);

        return $databaseImport->where([
            ["_id", "=", $id]
        ])->first();

    }

    // public static function insert($auth, $data) 
    // {

    //     $mytime = Carbon::now();

    //     $arr = [
    //         "file" => $data->file,
    //         "group" => [
    //             "_id" => $auth->_id,
    //             "name" => $auth->name
    //         ],
    //         "row" => 0,
    //         "status" => $data->status,
    //         "website" => [
    //             "_id" => "",
    //             "name" => ""
    //         ],
    //         "created" => [
    //             "timestamp" => $mytime->toDateTimeString(),
    //             "user" => [
    //                 "_id" => $auth->_id,
    //                 "username" => $auth->username
    //             ]
    //         ],
    //         "modified" => [
    //             "timestamp" => $mytime->toDateTimeString(),
    //             "user" => [
    //                 "_id" => $auth->_id,
    //                 "username" => $auth->username
    //             ]
    //         ]
    //     ];


    //     return DB::table('databaseImport')
    //         ->insert($arr);
    // }

    
}
