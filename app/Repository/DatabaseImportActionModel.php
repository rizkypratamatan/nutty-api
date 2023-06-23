<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\DatabaseImportAction;


class DatabaseImportActionModel {


    public static function delete($data, $nucode) {

        $data->setTable("databaseImportAction_" . $nucode);

        return $data->delete();

    }


    public static function findById($id, $nucode) {

        $model = new DatabaseImportAction();
        $model->setTable("databaseImportAction_" . $nucode);

        return $model->where([
            ["_id", "=", $id]
        ])->get();

    }


    public static function findOneByDatabaseImportId($databaseImportId, $nucode) {

        $databaseImportAction = new DatabaseImportAction();
        $databaseImportAction->setTable("databaseImportAction_" . $nucode);

        return $databaseImportAction->where([
            ["databaseImport._id", "=", $databaseImportId]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("databaseImportAction_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("databaseImportAction_" . $account->nucode);

        return $data->save();

    }


}
