<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Services\WorksheetService;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{

    public function getCrmData(Request $request){
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "worksheet", "view");
            
            return response()->json(WorksheetService::crmFindTable($request), 200);
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }





    public function resultUser(Request $request, $id) {

        // if(DataComponent::checkPrivilege($request, "worksheet", "view")) {

        //     $worksheetResponse = WorksheetService::findFilter($request, $id);

        //     $model = new stdClass();
        //     $model->filterDate = $worksheetResponse->filterDate;
        //     $model->userId = $id;
        //     $model->users = $worksheetResponse->users;
        //     $model->websites = $worksheetResponse->websites;

        //     return view("worksheet.result", [
        //         "layout" => (object)[
        //             "css" => [],
        //             "js" => ["worksheet.js"]
        //         ],
        //         "model" => $model
        //     ]);

        // } else {

        //     return redirect("/access-denied/");

        // }

    }


    public function callInitializeData(Request $request) {

        if(DataComponent::checkPrivilege($request, "worksheet", "view")) {

            return response()->json(WorksheetService::callInitializeData($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function initializeData(Request $request) {

        if(DataComponent::checkPrivilege($request, "worksheet", "view")) {

            return response()->json(WorksheetService::initializeData($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }

    public function resultTable(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "worksheet", "view");
            
            return response()->json(WorksheetService::resultFindTable($request), 200);
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);

    }


    // public function start(Request $request) {

    //     if(DataComponent::checkPrivilege($request, "worksheet", "edit")) {

    //         return response()->json(WorksheetService::start($request), 200);

    //     } else {

    //         return response()->json(DataComponent::initializeAccessDenied(), 200);

    //     }

    // }


    // public function startCrm(Request $request) {

    //     if(DataComponent::checkPrivilege($request, "worksheetCrm", "edit")) {

    //         return response()->json(WorksheetService::start($request), 200);

    //     } else {

    //         return response()->json(DataComponent::initializeAccessDenied(), 200);

    //     }

    // }


    // public function update(Request $request) {

    //     if(DataComponent::checkPrivilege($request, "worksheet", "edit")) {

    //         return response()->json(WorksheetService::update($request), 200);

    //     } else {

    //         return response()->json(DataComponent::initializeAccessDenied(), 200);

    //     }

    // }
}
