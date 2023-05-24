<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseImportModel;
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
}
