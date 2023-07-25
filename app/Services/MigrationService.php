<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Jobs\MigrationJob;
use App\Models\DatabaseAccount;
use App\Models\License;
use App\Models\ReportUser;
use App\Models\ReportWebsite;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Website;
use App\Repositories\ReportUserRepository;
use App\Repositories\ReportWebsiteRepository;
use App\Repositories\WebsiteRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use MongoDB\BSON\UTCDateTime;
use stdClass;


class MigrationService {


    public static function generateLastDeposit() {

        $result = new stdClass();
        $result->response = "Failed to generate last deposit data";
        $result->result = false;

        $websites = Website::where([])->get();

        foreach($websites as $value) {

            $databaseAccount = new DatabaseAccount();
            $databaseAccount->setTable("databaseAccount_" . $value->_id);

            $update = [
                "deposit" => [
                    "average" => [
                        "amount" => 0.00,
                    ],
                    "first" => [
                        "amount" => 0.00,
                        "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1960-01-10 00:00:00"))
                    ],
                    "last" => [
                        "amount" => 0.00,
                        "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1960-01-10 00:00:00"))
                    ],
                    "total" => [
                        "amount" => 0.00,
                        "time" => 0
                    ]
                ]
            ];
            $databaseAccount->where([])->update($update, ["upsert" => false]);
            $databaseAccount->update($update, ["upsert" => false]);

        }

        return $result;

    }


    public static function generateUnclaimedDeposit() {

        $result = new stdClass();
        $result->response = "Failed to generate unclaimed deposit data";
        $result->result = false;

        $websites = Website::where([])->get();

        foreach($websites as $value) {

            if(!Schema::hasTable("nexusPlayerTransaction_" . $value->_id)) {

                Schema::create("nexusPlayerTransaction_" . $value->_id, function(Blueprint $table) {

                    DataComponent::createNexusPlayerTransactionIndex($table);

                });

            }

            if(!Schema::hasTable("unclaimedDeposit_" . $value->_id)) {

                Schema::create("unclaimedDeposit_" . $value->_id, function(Blueprint $table) {

                    DataComponent::createUnclaimedDepositIndex($table);

                });

            }

        }

        return $result;

    }


    private static function generateReportWebsite($nucode) {

        $reportUser = new ReportUser();
        $reportUser->setTable("reportUser_" . $nucode);
        $reportUsers = $reportUser->where([])->get();

        if(!$reportUsers->isEmpty()) {

            foreach($reportUsers as $valueChild1) {

                for($i = 0; $i < count($valueChild1->website["ids"]); $i++) {

                    $account = DataComponent::initializeSystemAccount();
                    $account->nucode = $nucode;

                    if(empty($valueChild1->status["names"][$i])) {

                        $valueChild1->status = [
                            "names" => array_replace($valueChild1->status["names"], array($i => "Pending")),
                            "totals" => $valueChild1->status["totals"]
                        ];

                        $reportUser = new ReportUser();
                        $reportUser->setTable("reportUser_" . $nucode);

                        $reportUserById = $reportUser->where([
                            ["_id", "=", $valueChild1->_id]
                        ])->first();

                        if(!empty($reportUserById)) {

                            $statusNames = $valueChild1->status["names"];
                            $statusTotals = $valueChild1->status["totals"];

                            $index = array_search("Pending", $valueChild1->status["names"]);

                            if(gettype($index) == "integer") {

                                $statusTotals[$index] += $valueChild1->status["totals"][$i];

                            } else {

                                $statusNames[$i] = "Pending";
                                $statusTotals[$i] = $valueChild1->status["totals"][$i];

                            }

                            $reportUserById->status = [
                                "names" => $statusNames,
                                "totals" => $statusTotals
                            ];
                            ReportUserRepository::update($account, $reportUserById);

                        }

                    }

                    $reportWebsiteByDateWebsiteId = ReportWebsiteRepository::findOneByDateWebsiteId($valueChild1->date, $nucode, $valueChild1->website["ids"][$i]);

                    if(!empty($reportWebsiteByDateWebsiteId)) {

                        $statusNames = $reportWebsiteByDateWebsiteId->status["names"];
                        $statusTotals = $reportWebsiteByDateWebsiteId->status["totals"];
                        $index = array_search($valueChild1->status["names"][$i], $reportWebsiteByDateWebsiteId->status["names"]);

                        if(gettype($index) == "integer") {

                            $statusTotals[$index] += $valueChild1->status["totals"][$i];

                        } else {

                            $statusNames[count($statusNames) - 1] = $valueChild1->status["names"][$i];
                            $statusTotals[count($statusTotals) - 1] = $valueChild1->status["totals"][$i];

                        }

                        $reportWebsiteByDateWebsiteId->status = [
                            "names" => $statusNames,
                            "totals" => $statusTotals
                        ];
                        ReportWebsiteRepository::update($account, $reportWebsiteByDateWebsiteId);

                    } else {

                        $reportWebsite = new ReportWebsite();
                        $reportWebsite->date = $valueChild1->date;
                        $reportWebsite->status = [
                            "names" => [$valueChild1->status["names"][$i]],
                            "totals" => [$valueChild1->status["totals"][$i]]
                        ];
                        $reportWebsite->total = $valueChild1->total;
                        $reportWebsite->website = [
                            "_id" => DataComponent::initializeObjectId($valueChild1->website["ids"][$i]),
                            "name" => $valueChild1->website["names"][$i]
                        ];
                        ReportWebsiteRepository::insert($account, $reportWebsite);

                    }

                }

            }

        }

    }


    public static function migrate() {

        $result = new stdClass();
        $result->response = "Failed to migrate data";
        $result->result = false;

        $users = User::where([])->get();

        foreach($users as $value) {

            $userById = User::where([
                ["_id", "=", $value->_id]
            ])->first();

            if(!empty($userById)) {

                $userById->privilege = [
                    "database" => $userById->privilege["database"],
                    "report" => $userById->privilege["report"],
                    "setting" => $userById->privilege["setting"],
                    "settingApi" => $userById->privilege["settingApi"],
                    "template" => "0000",
                    "user" => $userById->privilege["user"],
                    "userGroup" => $userById->privilege["userGroup"],
                    "userRole" => $userById->privilege["userRole"],
                    "website" => $userById->privilege["website"],
                    "worksheet" => $userById->privilege["worksheet"],
                    "worksheetCrm" => "0000"
                ];
                $userById->save();

            }

        }

        $userRoles = UserRole::where([])->get();

        foreach($userRoles as $value) {

            $userRoleById = UserRole::where([
                ["_id", "=", $value->_id]
            ])->first();

            if(!empty($userRoleById)) {

                $userRoleById->privilege = [
                    "database" => $userById->privilege["database"],
                    "report" => $userById->privilege["report"],
                    "setting" => $userById->privilege["setting"],
                    "settingApi" => $userById->privilege["settingApi"],
                    "template" => "0000",
                    "user" => $userById->privilege["user"],
                    "userGroup" => $userById->privilege["userGroup"],
                    "userRole" => $userById->privilege["userRole"],
                    "website" => $userById->privilege["website"],
                    "worksheet" => $userById->privilege["worksheet"],
                    "worksheetCrm" => "0000"
                ];
                $userRoleById->save();

            }

        }

        $result->response = "Data migrated";
        $result->result = true;

        return $result;

    }


}
