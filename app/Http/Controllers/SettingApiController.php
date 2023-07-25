<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\WebsiteModel;
use App\Services\WebsiteService;
use Illuminate\Http\Request;
use stdClass;


class SettingApiController extends Controller {


    public function sync(Request $request) {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "settingApi", "edit");
            return response()->json(WebsiteService::sync($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);

    }

    public function index(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "settingApi", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];
            $filter['name'] = !empty($request->name)?$request->name:0;
            $filter['nucode'] = !empty($request->nucode)?$request->nucode:0;
            $filter['type'] = !empty($request->type)?$request->type:0;
            $filter['status'] = !empty($request->status)?$request->status:"Active";
            
            $model =  new WebsiteModel();
            $data = $model->getAllWebsite($limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All Website',
                'data' => $data['data'],
                'total_data' => $data['total_data']
            ];
            
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
            DataComponent::checkPrivilege($request, "settingApi", "edit");
            return response()->json(WebsiteService::update($request, true), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }


}
