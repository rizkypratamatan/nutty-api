<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\WhatsappLogModel;
use App\Repository\WhatsappModel;
use Illuminate\Http\Request;

class WhatsappLogController extends Controller
{
    public function getChats(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "tools", "view");

            $limit = !empty($request->limit) ? $request->limit : 10;
            $offset = !empty($request->offset) ? $request->offset : 0;
            $filter = [];
            $filter['recipient'] = !empty($request->recipient) ? $request->recipient : "";
            $filter['message'] = !empty($request->message) ? $request->message : "";
            $filter['status'] = !empty($request->status) ? $request->status : "";
            
            $model =  new WhatsappLogModel($request);
            $data = $model->getAllChat($limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All Whatsapp Chat',
                // 'data' => $data
            ];

            $response = array_merge($data, $response);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function deleteChat(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "delete");

            $model =  new WhatsappLogModel($request);
            $data = $model->deleteChat($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete Whatsapp Chat',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete Whatsapp Chat',
                ];
            }
        } else {
            $response = $validation;
        }
        return response()->json($response, 200);
    }

    public function getChatById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "view");

            $model =  new WhatsappLogModel($request);
            $data = $model->getChatById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get whatsapp',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get whatsapp',
                    'data' => null
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendBulkChat(Request $request)
    {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "add");

            $model = new WhatsappLogModel($request);
            $resp = $model->sendBulkChat();

            $response = [
                'result' => true,
                'response' => "WhatsApp bulk chats has been queued!",
                'data' => false
            ];
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendSingleChat(Request $request)
    {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "tools", "add");

            $model = new WhatsappLogModel($request);
            $resp = $model->sendSingleChat();

            $response = $resp;
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function testSendBulkChat(Request $request)
    {

        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

        //check privilege
        //DataComponent::checkPrivilege($request, "whatsapp", "add");

        $model = new WhatsappLogModel($request);
        $resp = $model->testSendBulkChat();

        $response = [
            'result' => true,
            'response' => "WhatsApp bulk chats has been queued!",
            'data' => false
        ];

        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }

    public function testSendSingleChat(Request $request)
    {

        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

        //check privilege
        // DataComponent::checkPrivilege($request, "whatsapp", "add");

        $model = new WhatsappLogModel($request);
        $resp = $model->testSendSingleChat();

        $response = $resp;

        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }
}
