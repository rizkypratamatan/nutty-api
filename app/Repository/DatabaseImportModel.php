<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\DatabaseImport;


class DatabaseImportModel {

    public static function getAll($request, $limit, $offset){

        $account = AuthenticationComponent::toUser($request);
        $databaseImport = new DatabaseImport();
        $databaseImport->setTable("databaseImport_" . $account->nucode);
        
        $response = [
            "data" => null,
            "total_data" => 0
        ];
        // if(!empty($request->website)){
        //     $databaseImport = $databaseImport->where('website._id', $request->website);
        // }
        $databaseImport = $databaseImport->where([
            ["status", "!=", "Deleted"]
        ]);
        $databaseImport = $databaseImport->orderBy('created.timestamp', 'desc');
        $recordsTotal = $databaseImport->count("_id");
        $data = $databaseImport->take($limit)->skip($offset)->get();

        $response['data'] = $data;
        $response['total_data'] = $recordsTotal;

        return $response;
    }

    public static function delete($data, $nucode) {
        $data->setTable("databaseImport_" . $nucode);
        return $data->delete();

    }

    public static function findOneById($id, $nucode) {

        $databaseImport = new DatabaseImport();
        $databaseImport->setTable("databaseImport_" . $nucode);

        return $databaseImport->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("databaseImport_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("databaseImport_" . $account->nucode);

        return $data->save();

    }


}
