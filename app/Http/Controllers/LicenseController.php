<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Models\License;
use App\Repository\LicenseModel;
use App\Services\LicenseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class LicenseController extends Controller
{
    
    public function deleteLicense(Request $request)
    {
        // print_r($request->all());die();

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            // DataComponent::checkPrivilege($request, "license", "delete");

            // $userModel =  new LicenseModel($request);
            // $account = AuthenticationComponent::toUser($request);
            $data = LicenseModel::deleteLicense($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete license',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete license',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function updateLicense(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "edit");
            $auth = AuthenticationComponent::toUser($request);

            $userModel =  new LicenseModel($request);
            $user = $userModel->updateLicense($request);

            if ($user) {
                $response = [
                    'result' => true,
                    'response' => 'success update license',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update license',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addLicense(Request $request)
    {
        // $validation = AuthenticationComponent::validate($request);
        // LogComponent::response($request, $validation);

        // if ($validation->result) {
        //     //check privilege
        //     DataComponent::checkPrivilege($request, "license", "add");

            $account = AuthenticationComponent::toUser($request);

            $license = new License();
            $license->nucode = $request->nucode;
            $license->package = [
                "expired" => new UTCDateTime(),
                "payment" => [
                    "last" => new UTCDateTime(),
                    "next" => new UTCDateTime()
                ],
                "start" => new UTCDateTime(),
                "status" => "Trial",
                "trial" => new UTCDateTime(Carbon::now()->addDays(30))
            ];
            $license->user = [
                "primary" => [
                    "_id" => DataComponent::initializeObjectId("64aba39eb4a6305167001eb2"),
                    "avatar" => "",
                    "name" => "",
                    "username" => ""
                ],
                "total" => 1
            ];
            LicenseModel::insert($account, $license);

            
            $response = [
                'result' => true,
                'response' => 'success add license',
            ];

        // } else {
        //     $response = $validation;
        // }

        return response()->json($response, 200);
    
    }

    public function getLicense(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            // DataComponent::checkPrivilege($request, "license", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            $filter = [];

            $filter['nucode'] = !empty($request->nucode)?$request->nucode:"";
            $auth = AuthenticationComponent::toUser($request);

            $model =  new LicenseModel($request);
            $data = $model->getLicense($auth, $limit, $offset, $filter);

            $response = [
                'result' => true,
                'response' => 'Get All License',
                // 'dataUser' => $data
            ];

            $response = array_merge($data, $response);
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getLicenseById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "license", "view");

            $account = AuthenticationComponent::toUser($request);
            $data = LicenseModel::getLicenseById($request->id, $account);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get license by id',
                    'dataUser' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get license by id',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getTable(Request $request){
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "license", "view");
            
            return response()->json(LicenseService::getTable($request), 200);
           
            
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

}
