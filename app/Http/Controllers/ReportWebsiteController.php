<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\ReportWebsiteModel;
use App\Services\ReportWebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;


class ReportWebsiteController extends Controller
{

    public function websiteReport(Request $request)
    {
        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {

            //check privilege
            DataComponent::checkPrivilege($request, "report", "view");

            return response()->json(ReportWebsiteService::findTable($request), 200);
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public function index(Request $request)
    {

        if (DataComponent::checkPrivilege($request, "report", "view")) {

            $request->session()->forget("reportDateRangeFilter");

            $reportWebsiteResponse = ReportWebsiteService::findFilter($request);

            $model = new stdClass();
            $model->websites = $reportWebsiteResponse->websites;

            return view("report.website", [
                "layout" => (object)[
                    "css" => [],
                    "js" => ["report-website.js"]
                ],
                "model" => $model
            ]);
        } else {

            return redirect("/access-denied/");
        }
    }


    public function table(Request $request)
    {

        if (DataComponent::checkPrivilege($request, "report", "view")) {

            return response()->json(ReportWebsiteService::findTable($request), 200);
        } else {

            return response()->json(DataComponent::initializeAccessDenied(), 200);
        }
    }
}
