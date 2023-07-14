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



    public function callInitializeData(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "worksheet", "view");
            return response()->json(WorksheetService::callInitializeData($request), 200);
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);

    }


    public function initializeData(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "worksheet", "view");
            return response()->json(WorksheetService::newDataFindTable($request), 200);
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);

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


    public function update(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            return response()->json(WorksheetService::update($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function processWa(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            return response()->json(WorksheetService::processWa($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function processSms(Request $request){
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            return response()->json(WorksheetService::processSms($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function processEmail(Request $request){
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        if ($validation->result) {
            return response()->json(WorksheetService::processEmail($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
