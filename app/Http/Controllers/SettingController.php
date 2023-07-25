<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\SettingModel;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getAll(Request $request)
    {
        // print_r($request->all());die();
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "user", "view");

            $settingModel =  new SettingModel();
            $setting = $settingModel->getAll();

            $response = [
                'result' => true,
                'response' => 'Get All Setting',
                'dataSetting' => $setting['data'],
                'total_data' => $setting['total_data']
            ];
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function updateSetting(Request $request)

    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "setting", "edit");

            return response()->json(SettingService::update($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
