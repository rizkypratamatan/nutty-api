<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\LicenseModel;
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

            $userModel =  new LicenseModel($request);
            $user = $userModel->deleteLicense($request->id);

            if ($user) {
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
            DataComponent::checkPrivilege($request, "license", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $userModel =  new LicenseModel($request);
            $user = $userModel->getLicense($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All License',
                'dataUser' => $user
            ];
           
            
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
}
