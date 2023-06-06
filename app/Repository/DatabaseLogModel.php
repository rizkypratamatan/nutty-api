<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\DatabaseLog;
use MongoDB\BSON\Regex;


class DatabaseLogModel 
{


    public static function countDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $status, $userId, $username, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->raw(function($collection) use ($createdDateEnd, $createdDateStart, $name, $status, $userId, $username, $websiteId) {

            $query = self::lookupDatabaseAccountTable([], $websiteId);

            array_push($query, [
                '$match' => [
                    "user._id" => DataComponent::initializeObjectId($userId)
                ]
            ]);

            $query = self::filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $query, $status, $username);

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


    private static function filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $query, $status, $username) {

        if(!empty($createdDateEnd) && !empty($createdDateStart)) {

            array_push($query, [
                '$match' => [
                    "created.timestamp" => [
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

        if(!empty($username)) {

            array_push($query, [
                '$match' => [
                    'databaseAccount.username' => new Regex($username)
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

        return $query;

    }


    public static function findDatabaseAccountTable($createdDateEnd, $createdDateStart, $limit, $name, $start, $sorts, $status, $userId, $username, $websiteId) {

        $databaseLog = new DatabaseLog();
        $databaseLog->setTable("databaseLog_" . $websiteId);

        return $databaseLog->raw(function($collection) use ($createdDateEnd, $createdDateStart, $limit, $name, $start, $sorts, $status, $userId, $username, $websiteId) {

            $query = self::lookupDatabaseAccountTable([], $websiteId);

            array_push($query, [
                '$match' => [
                    "user._id" => DataComponent::initializeObjectId($userId)
                ]
            ]);

            $query = self::filterDatabaseAccountTable($createdDateEnd, $createdDateStart, $name, $query, $status, $username);

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
