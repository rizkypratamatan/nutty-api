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
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "databaseImport", "add");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new DatabaseImportModel();
            $data = $model->importDatabase($request, $auth);

            if ($data) {
                // DataComponent::initializeCollectionByWebsite($data->_id);
                $data = DatabaseService::importData($request);
                $response = [
                    'result' => true,
                    'response' => 'success add import database',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add import database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function initializeData(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new DatabaseImportModel();
            $data = $model->initializeData($request, $auth);
            $data->userGroup = UserGroupModel::findByStatus("Active");
            $data->websites = WebsiteModel::findByStatus("Active");

            if ($data) {
                // DataComponent::initializeCollectionByWebsite($data->_id);
                $response = [
                    'result' => true,
                    'response' => 'Database import data initialized',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'Failed to initialize database import data',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function historyDelete(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "databaseImport", "delete");
            // $auth = AuthenticationComponent::toUser($request);

            $model =  new DatabaseImportModel();
            $data = $model->historyDelete($request);

            if ($data) {
                // DataComponent::initializeCollectionByWebsite($data->_id);
                $data = DatabaseService::importData($request);
                $response = [
                    'result' => true,
                    'response' => 'success delete import database',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete import database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function deleteImportDatabase(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "databaseImport", "delete");

            $model =  new DatabaseImportModel($request);
            $data = $model->deleteImportDatabase($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete database',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

}
