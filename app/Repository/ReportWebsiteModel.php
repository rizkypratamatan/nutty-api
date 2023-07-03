<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\ReportUser;


class ReportWebsiteModel {


    public static function findOneByDateWebsiteId($date, $nucode, $websiteId) {

        $reportWebsite = new ReportUser();
        $reportWebsite->setTable("reportWebsite_" . $nucode);

        return $reportWebsite->where([
            ["date", "=", $date],
            ["website._id", "=", $websiteId]
        ])->first();

    }


    public static function findWebsiteTable($date, $nucode, $websiteId) {

        $reportWebsite = new ReportUser();
        $reportWebsite->setTable("reportWebsite_" . $nucode);

        return $reportWebsite->raw(function($collection) use ($date, $nucode, $websiteId) {

            $query = DataComponent::initializeReportFilterDateRange($date, []);

            if(!is_null($websiteId)) {

                array_push($query, [
                    '$match' => [
                        "website._id" => $websiteId
                    ]
                ]);

            }

            array_push($query, [
                '$group' => [
                    "_id" => '$website._id',
                    "date" => [
                        '$push' => '$date'
                    ],
                    "status" => [
                        '$push' => '$status'
                    ],
                    "total" => [
                        '$sum' => '$total'
                    ],
                    "website" => [
                        '$push' => '$website'
                    ]
                ]
            ]);

            return $collection->aggregate($query, ["allowDiskUse" => true]);

        });

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("reportWebsite_" . $account->nucode);

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("reportWebsite_" . $account->nucode);

        return $data->save();

    }


}
