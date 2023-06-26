<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\UserGroup;
use App\Repository\UserGroupModel;
use App\Repository\UserModel;
use App\Repository\WebsiteModel;
use stdClass;


class UserGroupService {


    public static function delete($request) {

        $result = new stdClass();
        $result->response = "Failed to delete user group data";
        $result->result = false;

        $userGroupById = UserGroupModel::findOneById($request->id);

        if(!empty($userGroupById)) {

            UserGroupModel::delete($userGroupById);

            $result->response = "User group data deleted";
            $result->result = true;

        } else {

            $result->response = "User group doesn't exist";

        }

        return $result;

    }


    public static function findData($request) {

        $result = new stdClass();
        $result->response = "Failed to find user group data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        if($account->nucode != "system") {

            $result->websites = WebsiteModel::findByNucodeStatus($account->nucode, "Active");

        } else {

            $result->websites = WebsiteModel::findByStatus("Active");

        }

        $result->response = "User group filter data found";
        $result->result = true;

        return $result;

    }


    public static function findTable($request) {

        $result = new stdClass();
        $result->draw = $request->draw;
        $result->websites = [];

        $account = DataComponent::initializeAccount($request);

        $defaultOrder = ["created.timestamp"];
        $userGroups = DataComponent::initializeTableQuery(new UserGroup(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        $userGroups = DataComponent::initializeTableData($account, $userGroups);

        $result->recordsTotal = $userGroups->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $userGroups->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;

    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize user group data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $result->userGroup = UserGroupModel::findOneById($request->id);

        if($account->nucode != "system") {

            $result->websites = WebsiteModel::findByNucodeStatus($account->nucode, "Active");

        } else {

            $result->websites = WebsiteModel::findByStatus("Active");

        }

        $result->response = "User group data initialized";
        $result->result = true;

        return $result;

    }


    public static function insert($request) {

        $result = new stdClass();
        $result->response = "Failed to insert user group data";
        $result->result = false;

        $validation = self::validateData($request);

        if($validation->result) {

            UserGroupModel::insert(DataComponent::initializeAccount($request), $validation->userGroup);

            $result->response = "User group data inserted";
            $result->result = true;

        } else {

            $result->response = $validation->response;

        }

        return $result;

    }


    public static function update($request) {

        $result = new stdClass();
        $result->response = "Failed to update user group data";
        $result->result = false;

        $validation = self::validateData($request);

        if($validation->result) {

            UserGroupModel::update(DataComponent::initializeAccount($request), $validation->userGroup);

            $update = [
                "group" => [
                    "_id" => DataComponent::initializeObjectId($validation->userGroup->_id),
                    "name" => $validation->userGroup->name,
                ]
            ];
            UserModel::updateByGroupId($validation->userGroup->_id, $update);

            $result->response = "User group data updated";
            $result->result = true;

        }

        return $result;

    }


    public static function updateWebsiteNames($websiteId, $websiteName) {

        $result = new stdClass();
        $result->response = "Failed to update user group website names data";
        $result->result = false;

        $userGroupByWebsiteIdsNotWebsiteNames = UserGroupModel::findOneByWebsiteIdsNotWebsiteNames($websiteId, $websiteName);

        while(!empty($userGroupByWebsiteIdsNotWebsiteNames)) {

            $index = array_search($websiteId, $userGroupByWebsiteIdsNotWebsiteNames->website["ids"]);

            if(gettype($index) == "integer") {

                $websiteNames = $userGroupByWebsiteIdsNotWebsiteNames->website["names"];
                $websiteNames[$index] = $websiteName;

                $userGroupByWebsiteIdsNotWebsiteNames->website = [
                    "ids" => $userGroupByWebsiteIdsNotWebsiteNames->website["ids"],
                    "names" => $websiteNames
                ];
                UserGroupModel::update(null, $userGroupByWebsiteIdsNotWebsiteNames);

            }

            $userGroupByWebsiteIdsNotWebsiteNames = UserGroupModel::findOneByWebsiteIdsNotWebsiteNames($websiteId, $websiteName);

        }

    }


    public static function validateData($request) {

        $result = new stdClass();
        $result->response = "Failed to validate user group data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, $request->nucode, []);

        $result->userGroup = new UserGroup();

        if(!is_null($request->id)) {

            $result->userGroup = UserGroupModel::findOneById($request->id);

            if(empty($result->userGroup)) {

                array_push($validation, false);

                $result->response = "User group doesn't exist";

            }

        }

        $result->userGroup->description = $request->description;
        $result->userGroup->name = $request->name;
        $result->userGroup->nucode = $request->nucode;
        $result->userGroup->status = $request->status;
        $result->userGroup->website = [
            "ids" => $request->websites,
            "names" => []
        ];

        $userGroupByNameNucode = UserGroupModel::findOneByNameNucode($request->name, $request->nucode);

        if(!empty($userGroupByNameNucode)) {

            if(!$request->id == $userGroupByNameNucode->_id) {

                array_push($validation, false);

                $result->response = "User group name already exist";

            }

        }

        if(empty($validation)) {

            if(is_null($result->userGroup->description)) {

                $result->userGroup->description = "";

            }

            $websitesInId = WebsiteModel::findInId($result->userGroup->website["ids"]);
            $websiteNames = [];

            foreach($websitesInId as $website) {

                array_push($websiteNames, $website->name);

            }

            $result->userGroup->website = [
                "ids" => $result->userGroup->website["ids"],
                "names" => $websiteNames
            ];

            $result->response = "User group data validated";
            $result->result = true;

        }

        return $result;

    }


}
