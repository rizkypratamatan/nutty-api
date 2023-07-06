<?php

namespace App\Http\Controllers;

use App\Repository\SMSModel;
use Illuminate\Http\Request;

class SmsController extends Controller
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

            $auth = AuthenticationComponent::toUser($request);

            $model =  new SMSModel($request);
            $data = $model->getAll($limit, $offset, $auth);

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

            $auth = AuthenticationComponent::toUser($request);

            $model =  new SMSModel($request);
            $data = $model->deleteChat($request->id, $auth);

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

            $auth = AuthenticationComponent::toUser($request);

            $model =  new SMSModel($request);
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

    public function sendBulkMessage(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result){

            //check privilege
            DataComponent::checkPrivilege($request, "sms", "add");

            $model = new SMSModel($request);
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

            $model = new SMSModel($request);
            $resp = $model->sendSingle();

            $response = $resp;
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    // public function deleteReceivedChat(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "delete");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->deleteReceivedChat($request->id);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function deleteSentChat(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "delete");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->deleteSentChat($request->id);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function deleteCampaign(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "delete");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->deleteWhatsappCampaign($request->id);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function getAccounts(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {

    //         $limit = !empty($request->limit)?$request->limit:10;
    //         $page = !empty($request->page)?$request->page:1;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "view");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->getAccounts($limit, $page);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function getPendingChats(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {

    //         $limit = !empty($request->limit)?$request->limit:10;
    //         $page = !empty($request->page)?$request->page:1;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "view");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->getPendingChats($limit, $page);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function getReceivedChats(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {

    //         $limit = !empty($request->limit)?$request->limit:10;
    //         $page = !empty($request->page)?$request->page:1;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "view");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->getReceivedChats($limit, $page);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function getSentChats(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {

    //         $limit = !empty($request->limit)?$request->limit:10;
    //         $page = !empty($request->page)?$request->page:1;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "view");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->getSentChats($limit, $page);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function getCampaigns(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result) {

    //         $limit = !empty($request->limit)?$request->limit:10;
    //         $page = !empty($request->page)?$request->page:1;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "view");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->getWhatsappCampaign($limit, $page);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
           
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function startCampaign(Request $request){
    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result){

    //         $campaign_id = !empty($request->campaign_id)?$request->campaign_id:0;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "add");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->startWhatsappCampaign($campaign_id);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
        
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }

    // public function stopCampaign(Request $request){

    //     $validation = AuthenticationComponent::validate($request);
    //     LogComponent::response($request, $validation);

    //     if ($validation->result){

    //         $campaign_id = !empty($request->campaign_id)?$request->campaign_id:0;
    //         //check privilege
    //         DataComponent::checkPrivilege($request, "whatsapp", "add");
            
    //         $model = new WhatsappModel();
    //         $resp = $model->stopWhatsappCampaign($campaign_id);

    //         $response = [
    //             'result' => $resp->status,
    //             'response' => $resp->message,
    //             'data' => $resp->data
    //         ];
        
    //     } else {
    //         $response = $validation;
    //     }

    //     return response()->json($response, 200);
    // }
}
