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

            $userModel =  new LicenseModel();
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

            $userModel =  new LicenseModel();
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
}
