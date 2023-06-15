<?php

namespace App\Http\Controllers;

use App\Repository\SmsLogModel;
use App\Repository\SMSModel;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function getMessages(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "sms", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;

            $model =  new SmsLogModel($request);
            $data = $model->getAll($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All Message Chat',
                'data' => $data
            ];
           
            
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
            DataComponent::checkPrivilege($request, "sms", "delete");

            $model =  new SmsLogModel($request);
            $data = $model->deleteChat($request->id);

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
            DataComponent::checkPrivilege($request, "sms", "view");

            $model =  new SmsLogModel($request);
            $data = $model->getById($request->id);

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

    public function sendBulkMessage(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result){

            //check privilege
            DataComponent::checkPrivilege($request, "sms", "add");

            $model = new SmsLogModel($request);
            $response = $model->sendBulk();

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendSingleMessage(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result){

            //check privilege
            DataComponent::checkPrivilege($request, "sms", "add");

            $model = new SmsLogModel($request);
            $resp = $model->sendSingle();

            $response = $resp;
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function sendTestBulkMessage(Request $request){

        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

            //check privilege
            // DataComponent::checkPrivilege($request, "sms", "add");

            $model = new SmsLogModel($request);
            $response = $model->sendTestBulk();

        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }

    public function sendTestSingleMessage(Request $request){

        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

            //check privilege
            // DataComponent::checkPrivilege($request, "sms", "add");

            $model = new SmsLogModel($request);
            $resp = $model->sendTestSingle();

            $response = $resp;
            
        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }

    
}
