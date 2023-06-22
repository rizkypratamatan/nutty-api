<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseImportModel;
use App\Repository\UserGroupModel;
use App\Repository\WebsiteModel;
use App\Services\DatabaseService;
use Illuminate\Http\Request;

class DatabaseImportController extends Controller
{
    public function importDatabase(Request $request)
    {
        DataComponent::checkPrivilege($request, "database", "add");

        if (!empty($request->file)) {
            $result = DatabaseService::importData($request);
            $response = [
                'result' => $result->result,
                'response' => $result->response,
            ];
        } else {
            $response = [
                'result' => false,
                'response' => 'failed import database',
            ];
        }
        
        return response()->json($response, 200);
    }


    public function historyDelete(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "delete")) {

            return response()->json(DatabaseImportService::historyDelete($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function historyTable(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "view")) {

            return response()->json(DatabaseImportService::historyFindTable($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function import(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "add")) {

            $result = new stdClass();
            $result->response = "Failed to import database data";
            $result->result = false;

            if(!empty($request->file)) {

                $result = DatabaseService::importData($request);

            }

            return response()->json($result, 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function initializeData(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "view")) {

            return response()->json(DatabaseImportService::initializeData($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }

}
