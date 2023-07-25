<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\License;
use App\Repository\LicenseModel;
use App\Repository\UserGroupModel;
use App\Repository\UserLogModel;
use App\Repository\UserModel;
use App\Repository\UserRoleModel;
use App\Repository\WebsiteModel;
use Illuminate\Support\Facades\Schema;
use stdClass;


class LicenseService {


    public static function delete($request) {

        $result = new stdClass();
        $result->response = "Failed to delete license data";
        $result->result = false;

        $websitesByNucode = WebsiteModel::findByNucode($request->nucode);

        foreach($websitesByNucode as $value) {

            Schema::dropIfExists("database_" . $value->_id);
            Schema::dropIfExists("databaseAccount_" . $value->_id);
            Schema::dropIfExists("databaseLog_" . $value->_id);

        }

        Schema::dropIfExists("databaseAttempt_" . $request->nucode);
        Schema::dropIfExists("databaseImport_" . $request->nucode);
        Schema::dropIfExists("databaseImportAction_" . $request->nucode);
        Schema::dropIfExists("playerAttempt_" . $request->nucode);
        Schema::dropIfExists("reportUser_" . $request->nucode);

        UserGroupModel::deleteByNucode($request->nucode);
        UserLogModel::deleteByNucode($request->nucode);
        UserModel::deleteByNucode($request->nucode);
        UserRoleModel::deleteByNucode($request->nucode);
        WebsiteModel::deleteByNucode($request->nucode);

        $licenseByNucode = LicenseModel::findOneByNucode($request->nucode);

        if(!empty($licenseByNucode)) {

            LicenseModel::delete($licenseByNucode);

            $result->response = "License data deleted";
            $result->result = true;

        } else {

            $result->response = "License data doesn't exist";

        }

        return $result;

    }


    public static function findTable($request) {

        $result = new stdClass();
        $result->draw = $request->draw;

        $defaultOrder = ["created.timestamp"];
        $licenses = DataComponent::initializeTableQuery(new License(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        $result->recordsTotal = $licenses->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $licenses->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;

    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize license data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $result->license = LicenseModel::findOneById($request->id);

        $result->response = "User data initialized";
        $result->result = true;

        return $result;

    }


    public static function update($request) {

        $result = new stdClass();
        $result->response = "Failed to update license data";
        $result->result = false;

        $validation = self::validateData($request);

        if($validation->result) {

            LicenseModel::update(DataComponent::initializeAccount($request), $validation->license);

            $result->response = "License data updated";
            $result->result = true;

        }

        return $result;

    }


    public static function validateData($request) {

        $result = new stdClass();
        $result->response = "Failed to validate license data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, "system", []);

        $result->license = new License();

        if(!is_null($request->id)) {

            $result->license = LicenseModel::findOneById($request->id);

            if(empty($result->license)) {

                array_push($validation, false);

                $result->response = "License doesn't exist";

            }

        }

        $result->license->user = [
            "primary" => $result->license->user["primary"],
            "total" => intval($request->user["total"])
        ];

        if(empty($validation)) {

            $result->response = "License data validated";
            $result->result = true;

        }

        return $result;

    }


}
