<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseModel;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function addDatabase(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            // check privilege
            DataComponent::checkPrivilege($request, "database", "add");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new DatabaseModel($request);
            $data = $model->addDatabase($request, $auth);

            if ($data) {
                DataComponent::initializeCollectionByWebsite($auth->_id);
                $response = [
                    'result' => true,
                    'response' => 'success add database', 
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function deleteDatabase(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "database", "delete");

            $model =  new DatabaseModel($request);
            $data = $model->deleteDatabase($request->id);

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

    public function getDatabase(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $model =  new DatabaseModel($request);
            $data = $model->getDatabase($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get Database',
                'data' => $data
            ];
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getDatabaseById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");

            $model =  new DatabaseModel($request);
            $data = $model->getDatabaseById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get database',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function updateDatabaseById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "edit");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new DatabaseModel($request);
            $data = $model->updateDatabaseById($request, $auth);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update database',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update database',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
