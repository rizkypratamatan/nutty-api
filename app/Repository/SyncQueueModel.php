<?php

namespace App\Repository;

use App\Components\DataComponent;
use App\Models\SyncQueue;


class SyncQueueModel {


    public static function delete($data) {

        return $data->delete();

    }


    public static function findOneByStatus($status) {

        return SyncQueue::where([
            ["status", "=", $status]
        ])->orderBy("created.timestamp", "ASC")->first();

    }


    public static function findOneByWebsiteId($websiteId) {

        return SyncQueue::where([
            ["website._id", "=", $websiteId]
        ])->first();

    }


    public static function findOneByTemplateId($templateId) {

        return SyncQueue::where([
            ["template._id", "=", $templateId]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;

    }


    public static function update($account, $data) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        return $data->save();

    }


}
