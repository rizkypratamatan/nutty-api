<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\License;
use App\Repositories\LicenseRepository;
use App\Repositories\UserGroupRepository;
use App\Repositories\UserLogRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\WebsiteRepository;
use Illuminate\Support\Facades\Schema;
use stdClass;


class LicenseService {


    // public static function delete($request) 
    // {

    //     $result = new stdClass();
    //     $result->response = "Failed to delete license data";
    //     $result->result = false;

    //     $websitesByNucode = WebsiteRepository::findByNucode($request->nucode);

    //     foreach($websitesByNucode as $value) {

    //         Schema::dropIfExists("database_" . $value->_id);
    //         Schema::dropIfExists("databaseAccount_" . $value->_id);
    //         Schema::dropIfExists("databaseLog_" . $value->_id);

    //     }

    //     Schema::dropIfExists("databaseAttempt_" . $request->nucode);
    //     Schema::dropIfExists("databaseImport_" . $request->nucode);
    //     Schema::dropIfExists("databaseImportAction_" . $request->nucode);
    //     Schema::dropIfExists("playerAttempt_" . $request->nucode);
    //     Schema::dropIfExists("report_" . $request->nucode);

    //     UserGroupRepository::deleteByNucode($request->nucode);
    //     UserLogRepository::deleteByNucode($request->nucode);
    //     UserRepository::deleteByNucode($request->nucode);
    //     UserRoleRepository::deleteByNucode($request->nucode);
    //     WebsiteRepository::deleteByNucode($request->nucode);

    //     $licenseByNucode = LicenseRepository::findOneByNucode($request->nucode);

    //     if(!empty($licenseByNucode)) {

    //         LicenseRepository::delete($licenseByNucode);

    //         $result->response = "License data deleted";
    //         $result->result = true;

    //     } else {

    //         $result->response = "License data doesn't exist";

    //     }

    //     return $result;

    // }


    public static function getTable($request) 
    {

        $result = new stdClass();
        // $result->draw = $request->draw;

        $defaultOrder = ["created.timestamp"];
        $licenses = DataComponent::initializeTableQuery(new License(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        $result->recordsTotal = $licenses->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $licenses->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;

    }


    // public static function initializeData($request) 
    // {

    //     $result = new stdClass();
    //     $result->response = "Failed to initialize license data";
    //     $result->result = false;

    //     $account = DataComponent::initializeAccount($request);

    //     $result->license = LicenseRepository::findOneById($request->id);

    //     $result->response = "User data initialized";
    //     $result->result = true;

    //     return $result;

    // }


    // public static function update($request) 
    // {

    //     $result = new stdClass();
    //     $result->response = "Failed to update license data";
    //     $result->result = false;

    //     $validation = self::validateData($request);

    //     if($validation->result) {

    //         LicenseRepository::update(DataComponent::initializeAccount($request), $validation->license);

    //         $result->response = "License data updated";
    //         $result->result = true;

    //     }

    //     return $result;

    // }


    // public static function validateData($request) 
    // {

    //     $result = new stdClass();
    //     $result->response = "Failed to validate license data";
    //     $result->result = false;

    //     $validation = DataComponent::checkNucode($request, "system", []);

    //     $result->license = new License();

    //     if(!is_null($request->id)) {

    //         $result->license = LicenseRepository::findOneById($request->id);

    //         if(empty($result->license)) {

    //             array_push($validation, false);

    //             $result->response = "License doesn't exist";

    //         }

    //     }

    //     $result->license->user = [
    //         "primary" => $result->license->user["primary"],
    //         "total" => intval($request->user["total"])
    //     ];

    //     if(empty($validation)) {

    //         $result->response = "License data validated";
    //         $result->result = true;

    //     }

    //     return $result;

    // }


}
