<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
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
            DataComponent::checkPrivilege($request, "whatsapp", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;

            $model =  new WhatsappLogModel($request);
            $data = $model->getAllChat($limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All Whatsapp Chat',
                'data' => $data
            ];
           
            
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
            DataComponent::checkPrivilege($request, "whatsapp", "delete");

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
            DataComponent::checkPrivilege($request, "whatsapp", "view");

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

    public function sendBulkChat(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result){

            //check privilege
            DataComponent::checkPrivilege($request, "whatsapp", "add");

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

    public function sendSingleChat(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result){

            //check privilege
            DataComponent::checkPrivilege($request, "whatsapp", "add");

            $model = new WhatsappLogModel($request);
            $resp = $model->sendSingleChat();

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
