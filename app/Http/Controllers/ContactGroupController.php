<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Repository\ContactGroupModel;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
{
    public function index(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;

            $account = AuthenticationComponent::toUser($request);
            // $account = AuthenticationComponent::systemUser();

            $data = ContactGroupModel::getAll($limit, $offset, $account);

            $response = [
                'result' => true,
                'response' => 'Get All Contact Group',
                'data' => $data
            ];
           
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
            //// check privilege
            DataComponent::checkPrivilege($request, "userGroup", "add");

            $account = AuthenticationComponent::toUser($request);
            // $account = AuthenticationComponent::systemUser();

            $data = ContactGroupModel::add($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success add contact group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add contact group',
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
            $data = ContactGroupModel::updateById($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update contact group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update contact group',
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
            $data = ContactGroupModel::delete($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete contact group',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete contact group',
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

            // check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "view");

            $account = AuthenticationComponent::toUser($request);
            
            $data = ContactGroupModel::getById($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get contact group',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'Data Not found',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
