<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\Database;


class DatabaseModel {


    public static function delete($data) {

        return $data->delete();

    }


    public static function findOneById($id, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByContactPhone($contactPhone, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["contact.phone", "=", $contactPhone]
        ])->first();

    }


    public static function findOneLikeName($name, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["name", "LIKE", "%" . $name . "%"],
        ])->orderBy("name", "DESC")->first();

    }


    public static function findOneWorksheetCrm($crmId, $status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", $crmId],
            ["status", "=", $status]
        ])->first();

    }


    public static function findOneWorksheetGroup($groupId, $status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["group._id", "=", $groupId],
            ["status", "=", $status],
            ["telemarketer._id", "=", "0"]
        ])->first();

    }


    public static function findOneWorksheetTelemarketer($status, $telemarketerId, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["status", "=", $status],
            ["telemarketer._id", "=", $telemarketerId]
        ])->first();

    }


    public static function findOneWorksheetWebsite($status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["status", "=", $status],
            ["telemarketer._id", "=", "0"]
        ])->first();

    }


    public static function insert($account, $data, $websiteId) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("database_" . $websiteId);

        $data->save();

        return $data;

    }


    public static function update($account, $data, $websiteId) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("database_" . $websiteId);

        return $data->save();

    }


}
