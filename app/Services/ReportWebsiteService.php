<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Repository\ReportWebsiteModel;
use App\Repository\ReportWebsiteRepository;
use App\Repository\UserGroupRepository;
use App\Repository\WebsiteRepository;
use Illuminate\Support\Facades\Log;
use stdClass;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;


class ReportWebsiteService {


    public static function findFilter($request) {

        $result = new stdClass();
        $result->filterDate = "";
        $result->response = "Failed to find filter website report data";
        $result->result = false;
        $result->websites = [];

        $account = DataComponent::initializeAccount($request);

        if($request->session()->has("reportDateRangeFilter")) {

            $result->filterDate = $request->session()->get("reportDateRangeFilter");

        }

        $userGroupById = UserGroupRepository::findOneById($account->group["_id"]);

        if(!empty($userGroupById)) {

            $result->websites = WebsiteRepository::findInId($userGroupById->website["ids"]);

        }

        $result->response = "Filter website report data found";
        $result->result = true;

        return $result;

    }


    public static function findTable($request) {

        $result = new stdClass();
        // $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $reportWebsites = ReportWebsiteModel::findWebsiteTable($request->date, $account->nucode, $request->website);

        $result->recordsTotal = $reportWebsites->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $reportWebsites->forPage(DataComponent::initializePage($request->offset, $request->limit), $request->limit);

        // if(!empty($request->date)) {

        //     $request->session()->put("reportDateRangeFilter", $request->date);

        // }

        return $result;

    }

    public static function detailFindTable($request)
    {

        $result = new stdClass();
        // $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $filterDateRange = DataComponent::initializeFilterDateRange($request->filter_date, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)->addDays(1)), new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00")));
        $result->recordsTotal = ReportWebsiteModel::countByWebsiteIdBetweenDate($filterDateRange->end, $account->nucode, $filterDateRange->start, $request->websiteId);
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = ReportWebsiteModel::findByWebsiteIdBetweenDate($filterDateRange->end, $request->limit, $account->nucode, DataComponent::initializePage($request->offset, $request->limit), $filterDateRange->start, $request->websiteId);

        return $result;
    }


}
