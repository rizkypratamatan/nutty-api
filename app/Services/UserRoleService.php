<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\UserRole;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use stdClass;


class UserRoleService {


    public static function delete($request) {

        $result = new stdClass();
        $result->response = "Failed to delete user role data";
        $result->result = false;

        $userRoleById = UserRoleRepository::findOneById($request->id);

        if(!empty($userRoleById)) {

            UserRoleRepository::delete($userRoleById);

            $result->response = "User role data deleted";
            $result->result = true;

        } else {

            $result->response = "User role doesn't exist";

        }

        return $result;

    }


    public static function findTable($request) {

        $result = new stdClass();
        $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $defaultOrder = ["created.timestamp"];
        $userRoles = DataComponent::initializeTableQuery(new UserRole(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        $userRoles = DataComponent::initializeTableData($account, $userRoles);

        $result->recordsTotal = $userRoles->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $userRoles->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;

    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize user role data";
        $result->result = false;

        $result->userRole = UserRoleRepository::findOneById($request->id);

        $result->response = "User role data initialized";
        $result->result = true;

        return $result;

    }


    public static function insert($request) {

        $result = new stdClass();
        $result->response = "Failed to insert user role data";
        $result->result = false;

        $validation = self::validateData($request);

        if($validation->result) {

            UserRoleRepository::insert(DataComponent::initializeAccount($request), $validation->userRole);

            $result->response = "User role data inserted";
            $result->result = true;

        }

        return $result;

    }


    public static function update($request) {

        $result = new stdClass();
        $result->response = "Failed to update user role data";
        $result->result = false;

        $validation = self::validateData($request);

        if($validation->result) {

            UserRoleRepository::update(DataComponent::initializeAccount($request), $validation->userRole);

            $update = [
                "privilege" => $validation->userRole->privilege,
                "role" => [
                    "_id" => DataComponent::initializeObjectId($validation->userRole->_id),
                    "name" => $validation->userRole->name
                ]
            ];
            UserRepository::updateByRoleId($validation->userRole->_id, $update);

            $result->response = "User role data updated";
            $result->result = true;

        }

        return $result;

    }


    public static function validateData($request) {

        $result = new stdClass();
        $result->response = "Failed to validate user role data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, $request->nucode, []);

        $result->userRole = new UserRole();

        if(!is_null($request->id)) {

            $result->userRole = UserRoleRepository::findOneById($request->id);

            if(empty($result->userRole)) {

                array_push($validation, false);

                $result->response = "User role doesn't exist";

            }

        }

        $result->userRole->description = $request->description;
        $result->userRole->name = $request->name;
        $result->userRole->nucode = $request->nucode;
        $result->userRole->privilege = [
            "database" => $request->privilege["database"],
            "report" => $request->privilege["report"],
            "setting" => $request->privilege["setting"],
            "settingApi" => $request->privilege["settingApi"],
            "template" => $request->privilege["template"],
            "user" => $request->privilege["user"],
            "userGroup" => $request->privilege["userGroup"],
            "userRole" => $request->privilege["userRole"],
            "website" => $request->privilege["website"],
            "worksheet" => $request->privilege["worksheet"],
            "worksheetCrm" => $request->privilege["worksheetCrm"]
        ];
        $result->userRole->status = $request->status;

        $userRoleByNameNucode = UserRoleRepository::findOneByNameNucode($request->name, $request->nucode);

        if(!empty($userRoleByNameNucode)) {

            if(!$request->id == $userRoleByNameNucode->_id) {

                array_push($validation, false);

                $result->response = "User role name already exist";

            }

        }

        if(empty($validation)) {

            if(is_null($result->userRole->description)) {

                $result->userRole->description = "";

            }

            $result->response = "User role data validated";
            $result->result = true;

        }

        return $result;

    }


}
