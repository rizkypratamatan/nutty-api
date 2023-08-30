<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\WebsiteModel;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function getWebsites(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            // DataComponent::checkPrivilege($request, "website", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];
            $filter['name'] = !empty($request->name)?$request->name:0;
            $filter['nucode'] = !empty($request->nucode)?$request->nucode:0;
            $filter['type'] = !empty($request->type)?$request->type:0;
            $filter['status'] = !empty($request->status)?$request->status:0;
            
            $account = DataComponent::initializeAccount($request);
            $model =  new WebsiteModel($request);
            $data = $model->getAllWebsite($account->nucode, $limit, $offset, $filter);

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

    public function addWebsite(Request $request)
    {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "website", "add");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new WebsiteModel();
            $data = $model->addWebsite($request);

            if ($data) {
                DataComponent::initializeCollectionByWebsite($data->_id);
                $response = [
                    'result' => true,
                    'response' => 'success add website',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add website',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function updateWebsiteById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "website", "edit");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new WebsiteModel();
            $data = $model->updateWebsiteById($request);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update website',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update website',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function deleteWebsite(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "website", "delete");

            $model =  new WebsiteModel($request);
            $data = $model->deleteWebsite($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete website',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete website',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getWebsiteById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "website", "view");

            $model =  new WebsiteModel($request);
            $data = $model->getWebsiteById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get website',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get website',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
