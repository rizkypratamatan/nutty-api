<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\Setting;
use App\Repository\SettingModel;
use Illuminate\Support\Facades\Schema;
use stdClass;


class SettingService
{


    // public static function delete($request) {

    //     $result = new stdClass();
    //     $result->response = "Failed to delete license data";
    //     $result->result = false;

    //     $websitesByNucode = WebsiteModel::findByNucode($request->nucode);

    //     foreach($websitesByNucode as $value) {

    //         Schema::dropIfExists("database_" . $value->_id);
    //         Schema::dropIfExists("databaseAccount_" . $value->_id);
    //         Schema::dropIfExists("databaseLog_" . $value->_id);

    //     }

    //     Schema::dropIfExists("databaseAttempt_" . $request->nucode);
    //     Schema::dropIfExists("databaseImport_" . $request->nucode);
    //     Schema::dropIfExists("databaseImportAction_" . $request->nucode);
    //     Schema::dropIfExists("playerAttempt_" . $request->nucode);
    //     Schema::dropIfExists("reportUser_" . $request->nucode);

    //     UserGroupModel::deleteByNucode($request->nucode);
    //     UserLogModel::deleteByNucode($request->nucode);
    //     UserModel::deleteByNucode($request->nucode);
    //     UserRoleModel::deleteByNucode($request->nucode);
    //     WebsiteModel::deleteByNucode($request->nucode);

    //     $licenseByNucode = LicenseModel::findOneByNucode($request->nucode);

    //     if(!empty($licenseByNucode)) {

    //         LicenseModel::delete($licenseByNucode);

    //         $result->response = "License data deleted";
    //         $result->result = true;

    //     } else {

    //         $result->response = "License data doesn't exist";

    //     }

    //     return $result;

    // }


    // public static function findTable($request) {

    //     $result = new stdClass();
    //     $result->draw = $request->draw;

    //     $defaultOrder = ["created.timestamp"];
    //     $licenses = DataComponent::initializeTableQuery(new License(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

    //     $result->recordsTotal = $licenses->count("_id");
    //     $result->recordsFiltered = $result->recordsTotal;

    //     $result->data = $licenses->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

    //     return $result;

    // }


    // public static function initializeData($request) {

    //     $result = new stdClass();
    //     $result->response = "Failed to initialize license data";
    //     $result->result = false;

    //     $account = DataComponent::initializeAccount($request);

    //     $result->license = LicenseModel::findOneById($request->id);

    //     $result->response = "User data initialized";
    //     $result->result = true;

    //     return $result;

    // }


    public static function update($request)
    {

        $result = new stdClass();
        $result->response = "Failed to update setting data";
        $result->result = false;

        $validation = self::validateData($request);

        if ($validation->result) {

            SettingModel::update(DataComponent::initializeAccount($request), $validation->setting);

            $result->response = "Setting data updated";
            $result->result = true;
        }

        return $result;
    }


    public static function validateData($request)
    {

        $result = new stdClass();
        $result->response = "Failed to validate setting data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, "system", []);

        $result->setting = new Setting();

        // if (!is_null($request->id)) {

        //     $result->setting = SettingModel::findOneById($request->id);

        // if (empty($result->setting)) {

        //     array_push($validation, false);

        //     $result->response = "Setting doesn't exist";
        // }
        // }

        if (empty($validation)) {

            $result->response = "Setting data validated";
            $result->result = true;
        }

        return $result;
    }
}
