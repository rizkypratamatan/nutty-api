<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Repository\MessageTemplateModel;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];

            $filter['name'] = !empty($request->name)?$request->name:0;
            $auth = AuthenticationComponent::toUser($request);

            // $data = MessageTemplateModel::getAll($auth, $limit, $offset, $account);

            $userModel =  new MessageTemplateModel($request);
            $data = $userModel->getAll($auth, $limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All Message Template',
                // 'data' => $data
            ];
            $response = array_merge($data, $response);
           
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "add");

            $account = AuthenticationComponent::toUser($request);

            $data = MessageTemplateModel::add($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success add template',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add template',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function update(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "edit");

            $account = AuthenticationComponent::toUser($request);

            $data = MessageTemplateModel::updateById($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update template',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update template',
                ];
            }

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "delete");

            $account = AuthenticationComponent::toUser($request);
            $data = MessageTemplateModel::delete($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete template',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete template',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function show(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "view");

            $account = AuthenticationComponent::toUser($request);
            $data = MessageTemplateModel::getById($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get template',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get template',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
