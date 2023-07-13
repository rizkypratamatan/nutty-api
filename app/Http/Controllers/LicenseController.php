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
            // DataComponent::checkPrivilege($request, "license", "delete");

            // $userModel =  new LicenseModel($request);
            // $account = AuthenticationComponent::toUser($request);
            $data = LicenseModel::deleteLicense($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete license',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete license',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function updateLicense(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "edit");
            $auth = AuthenticationComponent::toUser($request);

            $userModel =  new LicenseModel($request);
            $user = $userModel->updateLicense($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success update license',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update license',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addLicense(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "add");
            $auth = AuthenticationComponent::toUser($request);

            $userModel =  new LicenseModel($request);
            $user = $userModel->addLicense($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success add license',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add license',
                ];
            }
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

            $userModel =  new LicenseModel($request);
            $user = $userModel->getLicenseById($request->id);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success get license by id',
                    'dataUser' => $user
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get license by id',
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

}
