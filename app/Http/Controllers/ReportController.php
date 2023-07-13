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
use App\Services\ReportUserService;
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

            return response()->json(ReportUserService::findTable($request), 200);

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

            // $model =  new ReportModel($request);
            // $data = $model->getReportById($request->id);

            // if ($data) {
            //     $response = [
            //         'result' => true,
            //         'response' => 'success get report',
            //         'dataUser' => $data
            //     ];
            // } else {
            //     $response = [
            //         'result' => false,
            //         'response' => 'failed get report',
            //     ];
            // }

            return response()->json(ReportUserService::detailFindTable($request), 200);

        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function detailTable(Request $request) {

        if(DataComponent::checkPrivilege($request, "report", "view")) {

            return response()->json(ReportUserService::detailFindTable($request), 200);

        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);

        }

    }

    public static function detailFindTable($request) {

        $result = new stdClass();
        $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $filterDateRange = DataComponent::initializeFilterDateRange($request->columns[1]["search"]["value"], new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)->addDays(1)), new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00")));
        $result->recordsTotal = ReportUserRepository::countByUserIdBetweenDate($filterDateRange->end, $account->nucode, $filterDateRange->start, $request->userId);
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = ReportUserRepository::findByUserIdBetweenDate($filterDateRange->end, $request->length, $account->nucode, DataComponent::initializePage($request->start, $request->length), $filterDateRange->start, $request->userId);

        return $result;

    }


    public static function findFilter($request, $userId) {

        $result = new stdClass();
        $result->filterDate = "";
        $result->response = "Failed to find filter user report data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $result->report = ReportUserRepository::findOneByUserId($account->nucode, $userId);

        if($request->session()->has("reportDateRangeFilter")) {

            $result->filterDate = $request->session()->get("reportDateRangeFilter");

        }

        $result->response = "Filter user report data found";
        $result->result = true;

        return $result;

    }


    public static function findTable($request) {

        $result = new stdClass();
        $result->draw = $request->draw;
        $result->recordsTotal = 0;

        $account = DataComponent::initializeAccount($request);

        $countUserTable = ReportUserRepository::countUserTable($request->columns[0]["search"]["value"], $request->columns[2]["search"]["value"], $account->nucode, $request->columns[1]["search"]["value"]);

        if(!$countUserTable->isEmpty()) {

            $result->recordsTotal = $countUserTable[0]->count;

        }

        $result->recordsFiltered = $result->recordsTotal;

        $result->data = ReportUserRepository::findUserTable($request->columns[0]["search"]["value"], $request->length, $request->columns[2]["search"]["value"], $account->nucode, $request->start, $request->columns[1]["search"]["value"]);

        if($account->nucode != "system") {

            $result->userGroups = UserGroupRepository::findByNucodeStatus($account->nucode, "Active");

        } else {

            $result->userGroups = UserGroupRepository::findByStatus("Active");

        }

        if(!empty($request->columns[0]["search"]["value"])) {

            $request->session()->put("reportDateRangeFilter", $request->columns[0]["search"]["value"]);

        }

        return $result;

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
