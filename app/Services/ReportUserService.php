<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Repository\ReportUserModel;
use App\Repository\UserGroupModel;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;
use stdClass;


class ReportUserService
{


    public static function detailFindTable($request)
    {

        $result = new stdClass();
        // $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $filterDateRange = DataComponent::initializeFilterDateRange($request->filter_date, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)->addDays(1)), new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00")));
        $result->recordsTotal = ReportUserModel::countByUserIdBetweenDate($filterDateRange->end, $account->nucode, $filterDateRange->start, $request->userId);
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = ReportUserModel::findByUserIdBetweenDate($filterDateRange->end, $request->limit, $account->nucode, DataComponent::initializePage($request->offset, $request->limit), $filterDateRange->start, $request->userId);

        return $result;
    }


    public static function findFilter($request, $userId)
    {

        $result = new stdClass();
        $result->filterDate = "";
        $result->response = "Failed to find filter user report data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $result->report = ReportUserModel::findOneByUserId($account->nucode, $userId);

        if ($request->session()->has("reportDateRangeFilter")) {

            $result->filterDate = $request->session()->get("reportDateRangeFilter");
        }

        $result->response = "Filter user report data found";
        $result->result = true;

        return $result;
    }


    public static function findTable($request)
    {

        $result = new stdClass();
        $result->draw = $request->draw;
        $result->recordsTotal = 0;

        $account = DataComponent::initializeAccount($request);

        $countUserTable = ReportUserModel::countUserTable($request->date, $request->name, $account->nucode, $request->username);

        if (!$countUserTable->isEmpty()) {

            $result->recordsTotal = $countUserTable[0]->count;
        }

        $result->recordsFiltered = $result->recordsTotal;

        $result->data = ReportUserModel::findUserTable($request->date, $request->limit, $request->name, $account->nucode, $request->offset, $request->username);

        if ($account->nucode != "system") {

            $result->userGroups = UserGroupModel::findByNucodeStatus($account->nucode, "Active");
        } else {

            $result->userGroups = UserGroupModel::findByStatus("Active");
        }

        return $result;
    }
}
