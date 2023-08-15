<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseModel;
use App\Services\DatabaseService;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function getDatabase(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            
            $data = DatabaseModel::getAll($request, $limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All Data',
                'data' => $data['data'],
                'total_data' => $data['total_data']
            ];
           
        } else {
            $response = $validation;
        }
        return response()->json($response, 200);
    }

    public function delete(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "delete")) {

            return response()->json(DatabaseService::delete($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function initializeData(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "view")) {

            return response()->json(DatabaseService::initializeData(), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function insert(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "add")) {

            return response()->json(DatabaseService::insert($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }


    public function table(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            
            return response()->json(DatabaseService::findTable($request), 200);
           
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }


    public function update(Request $request) {

        if(DataComponent::checkPrivilege($request, "database", "edit")) {

            return response()->json(DatabaseService::update($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }
}
