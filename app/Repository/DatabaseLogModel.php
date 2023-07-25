<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\DatabaseLog;
use MongoDB\BSON\Regex;


class DatabaseLogModel 
{

    public static function countDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $phone, $status, $userId, $username, $websiteId, $whatsapp) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->raw(function($collection) use ($createdDateEnd, $createdDateStart, $name, $phone, $status, $userId, $username, $websiteId, $whatsapp) {

            $query = self::lookupDatabaseAccountTable([], $websiteId);

            if($userId){
                array_push($query, [
                    '$match' => [
                        "user._id" => DataComponent::initializeObjectId($userId)
                    ]
                ]);
            }
            

            $query = self::filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $phone, $query, $status, $username, $whatsapp);

            array_push($query, [
                '$count' => "count"
            ]);

            return $collection->aggregate($query, ["allowDiskUse" => true]);

        });

    }


    public static function deleteByDatabaseId($databaseId, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->where([
            ["database._id", "=", $databaseId]
        ])->delete();

    }


    private static function filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $phone, $query, $status, $username, $whatsapp) {

        if(!empty($createdDateEnd) && !empty($createdDateStart)) {

            array_push($query, [
                '$match' => [
                    "modified.timestamp" => [
                        '$gte' => $createdDateStart,
                        '$lte' => $createdDateEnd
                    ]
                ]
            ]);

        }

        if(!empty($name)) {

            array_push($query, [
                '$match' => [
                    'database.name' => new Regex($name)
                ]
            ]);

        }

        if(!empty($phone)) {

            array_push($query, [
                '$match' => [
                    'database.contact.phone' => new Regex($phone)
                ]
            ]);

        }

        if(!empty($status)) {

            array_push($query, [
                '$match' => [
                    "status" => $status
                ]
            ]);

        }

        if(!empty($username)) {

            array_push($query, [
                '$match' => [
                    'databaseAccount.username' => new Regex($username)
                ]
            ]);

        }

        if(!empty($whatsapp)) {

            array_push($query, [
                '$match' => [
                    'database.contact.whatsapp' => new Regex($whatsapp)
                ]
            ]);

        }

        return $query;

    }


    public static function findDatabaseAccountTable($createdDateEnd, $createdDateStart, $limit, $name, $phone, $start, $sorts, $status, $userId, $username, $websiteId, $whatsapp) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->raw(function($collection) use ($createdDateEnd, $createdDateStart, $limit, $name, $phone, $start, $sorts, $status, $userId, $username, $websiteId, $whatsapp) {

            $query = self::lookupDatabaseAccountTable([], $websiteId);

            if($userId){
                array_push($query, [
                    '$match' => [
                        "user._id" => DataComponent::initializeObjectId($userId)
                    ]
                ]);
            }
            

            $query = self::filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $phone, $query, $status, $username, $whatsapp);

            foreach($sorts as $sort) {

                array_push($query, [
                    '$sort' => [
                        $sort["field"] => $sort["direction"]
                    ]
                ]);

            }

            array_push($query, [
                '$skip' => $start
            ]);

            array_push($query, [
                '$limit' => $limit
            ]);

            return $collection->aggregate($query, ["allowDiskUse" => true]);

        });

    }


    public static function findLastByDatabaseId($databaseId, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->where([
            ["database._id", "=", $databaseId]
        ])->orderBy("created.timestamp", "DESC")->first();

    }


    public static function findLastByDatabaseIdUserId($databaseId, $userId, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->where([
            ["database._id", "=", $databaseId],
            ["user._id", "=", $userId]
        ])->orderBy("created.timestamp", "DESC")->first();

    }


    public static function findOneById($id, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function insert($account, $data, $websiteId) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("databaseLog_" . $websiteId);

        $data->save();

        return $data;

    }


    private static function lookupDatabaseAccountTable($query, $websiteId) {

        array_push($query, [
            '$lookup' => [
                "from" => "database_" . $websiteId,
                "localField" => "database._id",
                "foreignField" => "_id",
                "pipeline" => [],
                "as" => "database"
            ]
        ]);

        array_push($query, [
            '$lookup' => [
                "from" => "databaseAccount_" . $websiteId,
                "localField" => "database._id",
                "foreignField" => "database._id",
                "as" => "databaseAccount"
            ]
        ]);

        return $query;

    }


    public static function update($account, $data, $websiteId) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("databaseLog_" . $websiteId);

        return $data->save();

    }


}
