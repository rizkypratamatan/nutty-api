<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\ReportUser;
use MongoDB\BSON\Regex;


class ReportUserModel {


    public static function countByUserIdBetweenDate($endDate, $nucode, $startDate, $userId) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->where([
            ["date", ">=", $startDate],
            ["date", "<=", $endDate],
            ["user._id", "=", $userId]
        ])->count("_id");

    }


    public static function countUserTable($date, $name, $nucode, $username) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->raw(function($collection) use ($date, $name, $username) {

            $query = self::initializeUserTable($date, $name, $username);

            array_push($query, [
                '$group' => [
                    "_id" => '$user._id'
                ]
            ]);

            array_push($query, [
                '$count' => "count"
            ]);

            return $collection->aggregate($query, ["allowDiskUse" => true]);

        });

    }


    public static function delete($id) {

        ReportUser::find($id)->delete();

    }


    public static function findByUserIdBetweenDate($endDate, $length, $nucode, $page, $startDate, $userId) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->where([
            ["date", ">=", $startDate],
            ["date", "<=", $endDate],
            ["user._id", "=", $userId]
        ])->forPage($page, $length)->get();

    }


    public static function findOneByDateUserId($date, $nucode, $userId) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->where([
            ["date", "=", $date],
            ["user._id", "=", $userId]
        ])->first();

    }


    public static function findOneByUserId($nucode, $userId) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->where([
            ["user._id", "=", $userId]
        ])->first();

    }


    public static function findUserTable($date, $limit, $name, $nucode, $start, $username) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);

        return $reportUser->raw(function($collection) use ($date, $limit, $name, $start, $username) {

            $query = self::initializeUserTable($date, $name, $username);

            array_push($query, [
                '$group' => [
                    "_id" => '$user._id',
                    "date" => [
                        '$push' => '$date'
                    ],
                    "status" => [
                        '$push' => '$status'
                    ],
                    "total" => [
                        '$sum' => '$total'
                    ],
                    "user" => [
                        '$push' => '$user'
                    ],
                    "website" => [
                        '$push' => '$website'
                    ]
                ]
            ]);

            array_push($query, [
                '$skip' => $start
            ]);

            array_push($query, [
                '$limit' => $limit
            ]);

            return $collection->aggregate($query, ["allowDiskUse" => true]);

        });

    }


    private static function initializeUserTable($date, $name, $username) {

        $result = DataComponent::initializeReportFilterDateRange($date, []);

        if(!empty($name)) {

            array_push($result, [
                '$match' => [
                    "user.name" => new Regex($name)
                ]
            ]);

        }

        if(!empty($username)) {

            array_push($result, [
                '$match' => [
                    "user.username" => new Regex($username)
                ]
            ]);

        }

        return $result;

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("reportUser_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("reportUser_" . $account->nucode);

        return $data->save();

    }


}
