<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\SyncQueue;
use App\Models\Website;
use App\Repository\SyncQueueModel;
use App\Repository\WebsiteModel;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;
use stdClass;


class WebsiteService {


    public static function delete($request) {

        $result = new stdClass();
        $result->response = "Failed to delete website data";
        $result->result = false;

        $websiteById = WebsiteModel::findOneById($request->id);

        if(!empty($websiteById)) {

            WebsiteModel::delete($websiteById);

            $result->response = "Website data deleted";
            $result->result = true;

        } else {

            $result->response = "Website doesn't exist";

        }

        return $result;

    }


    public static function findData($id) {

        $result = new stdClass();
        $result->response = "Failed to find website data";
        $result->result = false;

        $result->syncQueue = SyncQueueModel::findOneByWebsiteId($id);
        $result->website = WebsiteModel::findOneById($id);

        $result->response = "Website data found";
        $result->result = true;

        return $result;

    }


    public static function findTable($request, $active) {

        $result = new stdClass();
        $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $defaultOrder = ["created.timestamp"];
        $websites = DataComponent::initializeTableQuery(new Website(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        if($active) {

            $websites->where([
                ["status", "=", "Active"]
            ]);

        }

        $websites = DataComponent::initializeTableData($account, $websites);

        $result->recordsTotal = $websites->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $websites->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;

    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize website data";
        $result->result = false;

        $result->website = WebsiteModel::findOneById($request->id);

        $result->response = "Website data initialized";
        $result->result = true;

        return $result;

    }


    public static function insert($request) {

        $result = new stdClass();
        $result->response = "Failed to insert website data";
        $result->result = false;

        $validation = self::validateData($request, false);

        if($validation->result) {

            $websiteLast = WebsiteModel::insert(DataComponent::initializeAccount($request), $validation->website);
            DataComponent::initializeCollectionByWebsite($websiteLast->_id);

            $result->response = "Website data inserted";
            $result->result = true;

        } else {

            $result->response = $validation->response;

        }

        return $result;

    }


    public static function sync($request) {

        $result = new stdClass();
        $result->response = "Failed to sync website data";
        $result->result = false;

        $websiteById = WebsiteModel::findOneById($request->id);

        if(!empty($websiteById)) {

            if(!empty($websiteById->api["nexus"]["code"]) && !empty($websiteById->api["nexus"]["salt"]) && !empty($websiteById->api["nexus"]["url"]) && !empty($websiteById->start)) {

                //SystemService::syncPlayerTransaction($websiteById->_id);

                $websiteById->sync = "OnGoing";
                WebsiteModel::update(DataComponent::initializeAccount($request), $websiteById);

                $syncQueue = new SyncQueue();
                $syncQueue->date = new UTCDateTime(Carbon::now()->subDays(5));
                $syncQueue->nucode = $websiteById->nucode;
                $syncQueue->status = "OnGoing";
                $syncQueue->website = [
                    "_id" => DataComponent::initializeObjectId($websiteById->_id),
                    "name" => $websiteById->name
                ];
                SyncQueueModel::insert(DataComponent::initializeSystemAccount(), $syncQueue);

                $result->response = "Website data synced";
                $result->result = true;

            } else {

                $result->response = "Please fill in API credential and start date";

            }

        } else {

            $result->response = "Website doesn't exist";

        }

        return $result;

    }


    public static function update($request, $api) {

        $result = new stdClass();
        $result->response = "Failed to update website data";
        $result->result = false;

        $validation = self::validateData($request, $api);

        if($validation->result) {

            WebsiteModel::update(DataComponent::initializeAccount($request), $validation->website);
            //DataComponent::initializeCollectionByWebsite($validation->website->_id);

            UserGroupService::updateWebsiteNames($validation->website->_id, $validation->website->name);

            $result->response = "Website data updated";
            $result->result = true;

        } else {

            $result->response = $validation->response;

        }

        return $result;

    }


    public static function validateData($request, $api) {

        $result = new stdClass();
        $result->response = "Failed to validate website data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, $request->nucode, []);

        $result->website = new Website();

        if(!is_null($request->id)) {

            $result->website = WebsiteModel::findOneById($request->id);

            if(empty($result->website)) {

                array_push($validation, false);

                $result->response = "Website doesn't exist";

            }

        }

        if($api) {

            $result->website->api = [
                "nexus" => [
                    "code" => strtoupper($request->api["nexus"]["code"]),
                    "salt" => $request->api["nexus"]["salt"],
                    "url" => $request->api["nexus"]["url"]
                ]
            ];
            $result->website->start = new UTCDateTime(Carbon::createFromFormat("Y/m/d H:i:s", $request->start . " 00:00:00"));

        } else {

            $result->website->api = [
                "nexus" => [
                    "code" => "",
                    "salt" => "",
                    "url" => ""
                ]
            ];
            $result->website->description = $request->description;
            $result->website->name = $request->name;
            $result->website->nucode = $request->nucode;
            $result->website->start = new UTCDateTime(Carbon::createFromFormat("Y/m/d H:i:s", $request->start . "1970/01/10 00:00:00"));
            $result->website->status = $request->status;
            $result->website->sync = $request->sync;

            $websiteByNameNucode = WebsiteModel::findOneByNameNucode($request->name, $request->nucode);

            if(!empty($websiteByNameNucode)) {

                if(!$request->id == $websiteByNameNucode->id) {

                    array_push($validation, false);

                    $result->response = "Website name already exist";

                }

            }

        }

        if(empty($validation)) {

            if(!$api) {

                if(is_null($result->website->description)) {

                    $result->website->description = "";

                }

            }

            if(is_null($result->website->start)) {

                $result->website->start = new UTCDateTime(Carbon::createFromFormat("Y/m/d H:i:s", "1970/01/10 00:00:00"));

            }

            if(is_null($result->website->sync)) {

                $result->website->sync = "NoSync";

            }

            $result->response = "Website data validated";
            $result->result = true;

        }

        return $result;

    }


}
