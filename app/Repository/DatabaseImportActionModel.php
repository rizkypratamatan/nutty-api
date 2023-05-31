<?php

namespace App\Repositories;

use App\Components\DataComponent;
use App\Models\DatabaseImportAction;


class DatabaseImportActionModel 
{


    public static function delete($data, $nucode) 
    {

        $data->setTable("databaseImportAction_" . $nucode);

        return DatabaseImportAction::where('_id')->delete();

    }


    public static function findById($id, $nucode) 
    {

        $model = new DatabaseImportAction();
        $model->setTable("databaseImportAction_" . $nucode);

        return $model->where([
            ["_id", "=", $id]
        ])->get();

    }


    public static function findOneByDatabaseImportId($databaseImportId, $nucode) 
    {

        $databaseImportAction = new DatabaseImportAction();
        $databaseImportAction->setTable("databaseImportAction_" . $nucode);

        return $databaseImportAction->where([
            ["databaseImport._id", "=", $databaseImportId]
        ])->first();

    }


    public static function insert($auth, $data) 
    {

        $mytime = Carbon::now();
        $arr = [
            "accounts" =>$data->accounts,
            "databaseImport" => [
                "_id" => "0",
                "file" => "System"
            ],
            "crms" =>$data->crms,
            "inserts" =>$data->inserts,
            "groups" =>$data->groups,
            "phones" =>$data->phones,
            "telemarketers" =>$data->telemarketers,
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

        return DB::table('databaseImportAction')
            ->insert($arr);

    }


    public static function update($auth, $data) 
    {

        $mytime = Carbon::now();
        $arr = [
            "accounts" =>$data->accounts,
            "databaseImport" => [
                "_id" => "0",
                "file" => "System"
            ],
            "crms" =>$data->crms,
            "inserts" =>$data->inserts,
            "groups" =>$data->groups,
            "phones" =>$data->phones,
            "telemarketers" =>$data->telemarketers,
            // "created" => [
            //     "timestamp" => $mytime->toDateTimeString(),
            //     "user" => [
            //         "_id" => $auth->_id,
            //         "username" => $data->username
            //     ]
            // ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $data->username
                ]
            ]
        ];

        return DB::table('databaseImportAction')
        ->where('_id', $data->id)->update($arr);

    }


}
