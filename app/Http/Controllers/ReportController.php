<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Models\Report;
use App\Models\User;
use App\Repository\ReportModel;
use App\Repository\UserModel;
use App\Services\ReportService;
use Illuminate\Http\Request;
use stdClass;


class ReportController extends Controller {

    public function userReport(Request $request) 
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "report", "view");

            $model =  new ReportModel();
            $data = $model->userReport($request->id);
            $model->user->_id = $validation->report->user["_id"];
            $model->user->username = $validation->report->user["username"];

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get user report',
                    'dataUser' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get user report',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function addReport(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "report", "add");

            $model =  new ReportModel();
            $data = $model->addReport($request);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success add report',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed add report',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    
    }

    public function deleteReport(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);
        
        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "report", "delete");

            $model =  new ReportModel();
            $data = $model->deleteReport($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success delete report',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed delete report',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function updateReport(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "report", "edit");

            $model =  new ReportModel();
            $data = $model->updateReport($request);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success update report',
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed update report',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function getReportById(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "report", "view");

            $model =  new ReportModel();
            $data = $model->getReportById($request->id);

            if ($data) {
                $response = [
                    'result' => true,
                    'response' => 'success get report',
                    'dataUser' => $data
                ];
            } else {
                $response = [
                    'result' => false,
                    'response' => 'failed get report',
                ];
            }
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    // public function index(Request $request) 
    // {

    //     if(DataComponent::checkPrivilege($request, "report", "view")) {

    //         $request->session()->forget("reportDateRangeFilter");

    //         $model = new stdClass();

    //         return view("report.report", [
    //             "layout" => (object)[
    //                 "css" => [],
    //                 "js" => ["report.js"]
    //             ],
    //             "model" => $model
    //         ]);

    //     } else {

    //         return redirect("/access-denied/");

    //     }

    // }

    // public function table(Request $request) 
    // {

    //     if(DataComponent::checkPrivilege($request, "report", "view")) {

    //         return response()->json(ReportService::findTable($request), 200);

    //     } else {

    //         return response()->json(DataComponent::initializeAccessDenied(), 200);

    //     }

    // }


    // public function userTable(Request $request) 
    // {

    //     if(DataComponent::checkPrivilege($request, "report", "view")) {

    //         return response()->json(ReportService::findUserTable($request), 200);

    //     } else {

    //         return response()->json(DataComponent::initializeAccessDenied(), 200);

    //     }

    // }


}
