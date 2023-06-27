<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\EmailLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailLogController extends Controller
{
    public function getMessages(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "tools", "view");

            $limit = !empty($request->limit) ? $request->limit : 10;
            $offset = !empty($request->offset) ? $request->offset : 0;
            $filter = [];
            $filter['from_name'] = !empty($request->from_name) ? $request->from_name : "";
            $filter['email'] = !empty($request->email) ? $request->email : "";
            $filter['subject'] = !empty($request->subject) ? $request->subject : "";
            $filter['message'] = !empty($request->message) ? $request->message : "";
            $filter['status'] = !empty($request->status) ? $request->status : "";

            $auth = AuthenticationComponent::toUser($request);
            $model =  new EmailLogModel();
            $data = $model->getAll($limit, $offset, $auth, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All Message Chat',
                // 'data' => $data
            ];
            
            $response = array_merge($data, $response);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function deleteMessage(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "delete");

            $auth = AuthenticationComponent::toUser($request);

            $model =  new EmailLogModel();
            $data = $model->delete($request->id, $auth);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete Message Chat',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete Message Chat',
                ];
            }
        } else {
            $response = $validation;
        }
        return response()->json($response, 200);
    }

    public function getMessageById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "view");
            $auth = AuthenticationComponent::toUser($request);

            $model =  new EmailLogModel($request);
            $data = $model->getById($request->id, $auth);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get message',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get message',
                    'data' => null
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendBulkMessage(Request $request)
    {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            // check privilege
            DataComponent::checkPrivilege($request, "tools", "add");
            $model = new EmailLogModel();
            $response = $model->sendBulk($request);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendSingleMessage(Request $request)
    {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "add");


            $model = new EmailLogModel();
            $resp = $model->sendSingle($request);

            $response = $resp;
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
