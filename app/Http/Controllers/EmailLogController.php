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
            DataComponent::checkPrivilege($request, "email", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;

            $model =  new EmailLogModel($request);
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
            DataComponent::checkPrivilege($request, "email", "delete");

            $model =  new EmailLogModel($request);
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
            DataComponent::checkPrivilege($request, "email", "view");

            $model =  new EmailLogModel($request);
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

        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

            // check privilege
            // DataComponent::checkPrivilege($request, "email", "add");
            $model = new EmailLogModel();
            $response = $model->sendBulk($request);

        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }

    public function sendSingleMessage(Request $request){
        
        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result){

            //check privilege
            // DataComponent::checkPrivilege($request, "email", "add");
            

            $model = new EmailLogModel();
            $resp = $model->sendSingle($request);

            $response = $resp;
            
        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    }
}
