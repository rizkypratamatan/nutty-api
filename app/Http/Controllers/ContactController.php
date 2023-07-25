<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\LogComponent;
use App\Repository\ContactModel;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;

            $account = AuthenticationComponent::toUser($request);

            $data = ContactModel::getAll($limit, $offset, $account);

            $response = [
                'result' => true,
                'response' => 'Get All Contact',
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
            //check privilege
            // DataComponent::checkPrivilege($request, "userGroup", "add");

            $account = AuthenticationComponent::toUser($request);

            $data = ContactModel::add($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success add contact',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add contact',
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
            $data = ContactModel::updateById($request, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update contact',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update contact',
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
            $data = ContactModel::delete($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete contact',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete contact',
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
            $data = ContactModel::getById($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get contact',
                    'data' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'No Data Found',
                    'data' => null
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }
}
