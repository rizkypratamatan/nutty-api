<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function getWebsites(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "website", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $model =  new WebsiteModel();
            $data = $model->getAllWebsite($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All Website',
                'data' => $data
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

            $model =  new WebsitepModel();
            $data = $model->addWebsite($request, $auth);

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

    public function updateWebsiteId(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "website", "edit");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new UserGroupModel();
            $data = $model->updateUserGroupById($request, $auth);

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

            $model =  new UserGroupModel();
            $data = $model->deleteUserGroup($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete user group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete user group',
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

            $model =  new UserGroupModel();
            $data = $model->getUserGroupById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get user group',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get user group',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
