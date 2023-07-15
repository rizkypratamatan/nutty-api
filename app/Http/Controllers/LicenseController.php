<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\LicenseModel;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    
    public function deleteLicense(Request $request)
    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "license", "delete");

            return response()->json(LicenseService::delete($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getLicense(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            // DataComponent::checkPrivilege($request, "license", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];

            $filter['nucode'] = !empty($request->nucode)?$request->nucode:"";
            $auth = AuthenticationComponent::toUser($request);

            $model =  new LicenseModel($request);
            $data = $model->getLicense($auth, $limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All License',
                // 'dataUser' => $data
            ];

            $response = array_merge($data, $response);
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getLicenseById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "license", "view");

            $model =  new LicenseModel($request);
            $data = $model->getLicenseById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'License Found',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'License Not Found',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getTable(Request $request){
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "view");
            
            return response()->json(LicenseService::getTable($request), 200);
           
            
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
            DataComponent::checkPrivilege($request, "license", "view");
            return response()->json(LicenseService::initializeData($request), 200);
        } else {
            $response = $validation;
        }
        return response()->json($response, 200);

    }


    public function update(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "edit");
            return response()->json(LicenseService::update($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

}
