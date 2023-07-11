<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Repositories\ReportUserRepository;
use App\Repositories\UserGroupRepository;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;
use stdClass;


class ReportUserService {


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


}
