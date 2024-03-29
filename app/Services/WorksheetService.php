<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\DatabaseAccount;
use App\Models\DatabaseAttempt;
use App\Models\DatabaseLog;
use App\Models\EmailQueue;
use App\Models\MessageTemplate;
use App\Models\ReportUser;
use App\Models\ReportWebsite;
use App\Models\SmsQueue;
use App\Models\User;
use App\Models\WaQueue;
use App\Repository\DatabaseAccountModel;
use App\Repository\DatabaseAttemptModel;
use App\Repository\DatabaseLogModel;
use App\Repository\DatabaseModel;
use App\Repository\MessageTemplateModel;
use App\Repository\PlayerAttemptModel;
use App\Repository\ReportUserModel;
use App\Repository\ReportWebsiteModel;
use App\Repository\SettingModel;
use App\Repository\SMSModel;
use App\Repository\UserGroupModel;
use App\Repository\UserModel;
use App\Repository\WebsiteModel;
use App\Services\Gateway\SMSService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;
use stdClass;


class WorksheetService {


    public static function callInitializeData($request) {

        $result = new stdClass();
        $result->back = false;
        $result->databaseAccount = [];
        $result->databaseLog = null;
        $result->reference = "";
        $result->response = "Failed to initialize call worksheet data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $result->database = DatabaseModel::findOneById($request->id, $request->websiteId);

        if(!empty($result->database)) {

            $result->databaseAccount = DatabaseAccountModel::findOneByDatabaseId($result->database->_id, $request->websiteId);

            $result->databaseLog = DatabaseLogModel::findLastByDatabaseIdUserId($result->database->_id, $account->_id, $request->websiteId);

            if(!empty($result->databaseLog)) {

                $result->reference = $result->databaseLog->reference;

                if($result->databaseLog->status != "FollowUp" && $result->databaseLog->status != "Interested" && $result->databaseLog->status != "Pending" && $result->databaseLog->status != "Registered") {

                    $result->back = true;

                }

            }

        }

        $result->response = "Call worksheet data initialized";
        $result->result = true;

        return $result;

    }


    public static function crmFindTable($request) {

        $result = new stdClass();
        $auth = DataComponent::initializeAccount($request);

        $sorts = [
            [
                "field" => "created.timestamp",
                "direction" => 1
            ]
        ];

        $depositLastTimestamp = new UTCDateTime(Carbon::now()->subDays($request->days));

        $count = DatabaseAccountModel::countCrmTable($auth['_id'], $depositLastTimestamp, 100, $request->websiteId);

        if(!$count->isEmpty()) {

            $result->total_data = $count[0]->count;

        }

        $result->data = DatabaseAccountModel::findCrmTable($auth['_id'], $depositLastTimestamp, $request->limit, $sorts, $request->offset, $request->websiteId);

        return $result;

    }

    public static function newDataFindTable($request) {

        $result = new stdClass();
        $auth = DataComponent::initializeAccount($request);
        $result->total_data = 0;

        // $sorts = [
        //     [
        //         "field" => "created.timestamp",
        //         "direction" => 1
        //     ]
        // ];

        if($auth->type == "CRM") {
            $resp = DatabaseModel::findListWorksheetCrm($auth['username'], "Available", $request->limit, $request->offset, $request->websiteId);
            $result->data = $resp['data'];
            $result->total_data = $resp['total_data'];


        } else if($auth->type == "Telemarketer") {
            
            $resp = DatabaseModel::findListWorksheetTelemarketer($auth['username'], "Available", $request->limit, $request->offset, $request->websiteId);
            $result->data = $resp['data'];
            $result->total_data = $resp['total_data'];

        }
        if($result->total_data == 0) {

            $resp = DatabaseModel::findListWorksheetGroup($auth->group["_id"], 'Available', $request->limit, $request->offset, $request->websiteId);
            $result->total_data = $resp['total_data'];
            $result->data = $resp['data'];

            if($result->total_data == 0) {
                $resp = DatabaseModel::findListWorksheetWebsite("Available", $request->limit, $request->offset, $request->websiteId);
                $result->total_data = $resp['total_data'];
                $result->data = $resp['data'];

            }
        }

        return $result;
    }


    public static function findFilter($request, $id) {

        $result = new stdClass();
        $result->filterDate = "";
        $result->response = "Failed to find filter worksheet data";
        $result->result = false;
        $result->websites = [];

        $account = DataComponent::initializeAccount($request);

        if($request->session()->has("reportDateRangeFilter")) {

            $result->filterDate = $request->session()->get("reportDateRangeFilter");

        }

        $result->users = UserModel::findByNucode($account->nucode);

        if($id != null) {

            $userById = UserModel::findOneById($id);

            if(empty($userById)) {

                $reportByUserId = ReportUserModel::findOneByUserId($account->nucode, $id);

                if(!empty($reportByUserId)) {

                    $user = new User();
                    $user->_id = $reportByUserId->user["_id"];
                    $user->name = $reportByUserId->user["name"];
                    $user->username = $reportByUserId->user["username"];

                    $result->users = $result->users->push($user);

                }

            }

        }

        $userGroupById = UserGroupModel::findOneById($account->group["_id"]);

        if(!empty($userGroupById)) {

            $result->websites = WebsiteModel::findInId($userGroupById->website["ids"]);

        }

        $result->response = "Filter report data found";
        $result->result = true;

        return $result;

    }


    public static function generateReport($account, $date, $status, $website) {

        $reportUserByDateUserId = ReportUserModel::findOneByDateUserId($date, $account->nucode, $account->_id);

        if(!empty($reportUserByDateUserId)) {

            $reportUserByDateUserId->status = self::initializeStatusData($reportUserByDateUserId, $status);
            $reportUserByDateUserId->total += 1;
            $reportUserByDateUserId->website = self::initializeWebsiteData($reportUserByDateUserId, $website);
            ReportUserModel::update($account, $reportUserByDateUserId);

        } else {

            $reportUser = new ReportUser();
            $reportUser->date = $date;
            $reportUser->status = [
                "names" => [$status],
                "totals" => [1]
            ];
            $reportUser->total = 1;
            $reportUser->user = [
                "_id" => DataComponent::initializeObjectId($account->_id),
                "avatar" => $account->avatar,
                "name" => $account->name,
                "username" => $account->username
            ];
            $reportUser->website = [
                "ids" => [$website->_id],
                "names" => [$website->name],
                "totals" => [1]
            ];
            ReportUserModel::insert($account, $reportUser);

        }

        $reportWebsiteByDateWebsiteId = ReportWebsiteModel::findOneByDateWebsiteId($date, $account->nucode, $website->_id);

        if(!empty($reportWebsiteByDateWebsiteId)) {

            $reportWebsiteByDateWebsiteId->status = self::initializeStatusData($reportWebsiteByDateWebsiteId, $status);
            $reportWebsiteByDateWebsiteId->total += 1;
            ReportWebsiteModel::update($account, $reportWebsiteByDateWebsiteId);

        } else {

            $reportWebsite = new ReportWebsite();
            $reportWebsite->date = $date;
            $reportWebsite->status = [
                "names" => [$status],
                "totals" => [1]
            ];
            $reportWebsite->total = 1;
            $reportWebsite->website = [
                "_id" => DataComponent::initializeObjectId($website->_id),
                "name" => $website->name
            ];
            ReportWebsiteModel::insert($account, $reportWebsite);

        }

    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->back = false;
        $result->database = null;
        $result->databaseAccount = null;
        $result->databaseLog = null;
        $result->reference = "";
        $result->response = "Failed to initialize worksheet data";
        $result->result = false;
        $result->userGroup = null;

        $account = DataComponent::initializeAccount($request);

        if($request->websiteId) {

            $websiteById = WebsiteModel::findOneById($request->websiteId);

            if(!empty($websiteById)) {

                if($account->type == "CRM") {

                    $result->database = DatabaseModel::findOneWorksheetCrm($account->_id, "Available", $websiteById->_id);

                } else if($account->type == "Telemarketer") {

                    $result->database = DatabaseModel::findOneWorksheetTelemarketer("Available", $account->_id, $websiteById->_id);

                }

                if(empty($result->database)) {

                    $result->database = DatabaseModel::findOneWorksheetGroup($account->group["_id"], "Available", $websiteById->_id);

                    if(empty($result->database)) {

                        $result->database = DatabaseModel::findOneWorksheetWebsite("Available", $websiteById->_id);

                    }

                }

                if(!empty($result->database)) {

                    $result->databaseAccount = DatabaseAccountModel::findOneByDatabaseId($result->database->_id, $websiteById->_id);

                    $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($result->database->_id, $account->_id, $websiteById->_id);

                    if(!empty($databaseLogByDatabaseIdUserId)) {

                        $result->reference = $databaseLogByDatabaseIdUserId->reference;

                    }

                    $result->database->status = "Reserved";

                    if($account->type == "Telemarketer") {

                        $result->database->telemarketer = [
                            "_id" => $account->_id,
                            "avatar" => $account->avatar,
                            "name" => $account->name,
                            "username" => $account->username
                        ];

                    }

                    DatabaseModel::update($account, $result->database, $websiteById->_id);

                    $result->databaseAccount = DatabaseAccountModel::findOneByDatabaseId($result->database->_id, $websiteById->_id);

                    $databaseLog = new DatabaseLog();
                    $databaseLog->database = [
                        "_id" => DataComponent::initializeObjectId($result->database->_id),
                        "name" => $result->database->name
                    ];
                    $databaseLog->status = "Pending";
                    $databaseLog->user = [
                        "_id" => DataComponent::initializeObjectId($account->_id),
                        "avatar" => $account->avatar,
                        "name" => $account->name,
                        "username" => $account->username
                    ];
                    $databaseLog->website = [
                        "_id" => DataComponent::initializeObjectId($websiteById->_id),
                        "name" => $websiteById->name
                    ];
                    $result->databaseLog = DatabaseLogModel::insert($account, $databaseLog, $websiteById->_id);

                    $result->response = "Worksheet data initialized";
                    $result->result = true;

                } else {

                    $result->response = "Database empty";

                }

            } else {

                $result->response = "Website doesn't exist";

            }

        } else {

            $result->userGroup = UserGroupModel::findOneById($account->group["_id"]);

            $result->response = "Worksheet data initialized";
            $result->result = true;

        }

        return $result;

    }


    private static function initializeStatusData($data, $status) {

        $result = $data->status;

        $index = array_search($status, $data->status["names"]);

        if(gettype($index) == "integer") {

            $result["totals"][$index]++;

        } else {

            array_push($result["names"], $status);
            array_push($result["totals"], 1);

        }

        return $result;

    }


    private static function initializeWebsiteData($data, $website) {

        $result = $data->website;

        $index = array_search($website->_id, $data->website["ids"]);

        if(gettype($index) == "integer") {

            $result["totals"][$index]++;

        } else {

            array_push($result["ids"], $website->_id);
            array_push($result["names"], $website->name);
            array_push($result["totals"], 1);

        }

        return $result;

    }


    public static function resultFindTable($request) {

        $result = new stdClass();

        $account = DataComponent::initializeAccount($request);

        $websiteIds = [];
        
        $count = 0;
        $data = new Collection();

        $filterName = $request->filter_name;
        $filterPhone = $request->filter_phone;
        $filterStatus = $request->filter_status;
        $filterUser = $account->_id;
        $filterUsername = $request->filter_username;
        $filterWebsite = $request->filter_website;
        $filterWhatsapp = $request->filter_name;

        // $filterName = $filter[3]->search->value;
        // $filterPhone = $filter[4]->search->value;
        // $filterStatus = $filter[7]->search->value;
        // $filterUser = $account->_id;
        // $filterUsername = $filter[2]->search->value;
        // $filterWebsite = $filter[6]->search->value;
        // $filterWhatsapp = $filter[5]->search->value;

        if($account->username == "system" || $account->type == "Administrator") {

            

            if($account->username == "system") {

                $websiteIds = WebsiteModel::findIdAll();
                

            } else if($account->type == "Administrator") {

                $userById = UserModel::findOneById($filterUsername);

                if(!empty($userById)) {

                    $userGroupById = UserGroupModel::findOneById($userById->group["_id"]);

                    if(!empty($userGroupById)) {

                        $websiteIds = $userGroupById->website["ids"];

                    }

                }

            }

            

            // $filterName = $filter[4]->search->value;
            // $filterPhone = $filter[5]->search->value;
            // $filterStatus = $filter[8]->search->value;
            // $filterUser = $filter[2]->search->value;
            // $filterUsername = $filter[3]->search->value;
            // $filterWebsite = $filter[7]->search->value;
            // $filterWhatsapp = $filter[6]->search->value;;

            $filterName = $request->filter_name;
            $filterPhone = $request->filter_phone;
            $filterStatus = $request->filter_status;
            $filterUser = $request->filter_user;
            $filterUsername = $request->filter_username;
            $filterWebsite = $request->filter_website;
            $filterWhatsapp = $request->filter_whatsapp;

        } else {

            $userGroupById = UserGroupModel::findOneById($account->group["_id"]);

            if(!empty($userGroupById)) {

                $websiteIds = $userGroupById->website["ids"];

            }

        }
        // echo $filterWebsite." ok ";
        // print_r($websiteIds);die;


        $filterPhone = preg_replace("/[^0-9]/", '', $filterPhone);
        $filterWhatsapp = preg_replace("/[^0-9]/", '', $filterWhatsapp);
        
        if(!empty($websiteIds)) {
            
            $date = Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0);
            $filterDateRange = DataComponent::initializeFilterDateRange($request->filter_date, new UTCDateTime($date->addDays(1)), new UTCDateTime($date));

            foreach($websiteIds as $value) {

                $retrieve = true;

                if(!empty($filterWebsite) && $value != $filterWebsite) {

                    $retrieve = false;

                }

                if($retrieve) {
                    
                    $countResultTable = DatabaseLogModel::countDatabaseAccountTable($filterDateRange->end, $filterDateRange->start, $filterName, $filterPhone, $filterStatus, $filterUser, $filterUsername, $value, $filterWhatsapp);
                    
                    if(!$countResultTable->isEmpty()) {

                        $count += $countResultTable[0]->count;

                    }

                }

                $sorts = [
                    [
                        "field" => "created.timestamp",
                        "direction" => 1
                    ]
                ];

                // $order = DataComponent::initializeObject($request->order);

                // if($order[0]->column != 0) {

                //     if(strtoupper($order[0]->dir) == "ASC" || strtoupper($order[0]->dir) == "DESC") {

                //         $direction = 1;

                //         if(strtoupper($order[0]->dir) == "DESC") {

                //             $direction = -1;

                //         }

                //         $sorts = [
                //             [
                //                 "field" => $order[0]->column->data,
                //                 "direction" => $direction
                //             ]
                //         ];

                //     }

                // }

                if($retrieve) {
                    $data = $data->merge(DatabaseLogModel::findDatabaseAccountTable($filterDateRange->end, $filterDateRange->start, $request->limit, $filterName, $filterPhone, $request->offset, $sorts, $filterStatus, $filterUser, $filterUsername, $value, $filterWhatsapp));

                }

            }

        }

        $result->total_data = $count;

        $result->data = $data;

        return $result;

    }


    public static function start($request) {

        $result = new stdClass();
        $result->account = DataComponent::initializeAccount($request);
        $result->response = "Failed to start worksheet";
        $result->result = false;

        $request->session()->put("websiteId", $request->website["id"]);

        $result->response = "Database worksheet initialized";
        $result->result = true;

        return $result;

    }


    public static function update($request) {

        $result = new stdClass();
        $result->response = "Failed to update worksheet data";
        $result->data = "";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $databaseById = DatabaseModel::findOneById($request->id, $request->websiteId);

        if(!empty($databaseById)) {
            $databaseAttemptByContactPhone = DatabaseAttemptModel::findOneByContactPhone($databaseById->contact["phone"], $account->nucode);

            if(!empty($databaseAttemptByContactPhone)) {
                $result = self::updateDatabase($request, $databaseById, $databaseAttemptByContactPhone);

            }else{
                $databaseAttempt = new DatabaseAttempt();
                $databaseAttempt->contact = $databaseById->contact["phone"];
                $databaseAttempt->total = 0;
                $databaseAttempt->status = [
                    "names" => [], 
                    "totals" => []
                ];
                $databaseAttempt->website = [
                    "ids" => [],
                    "names" => [],
                    "totals" => []
                ];

                $databaseAttempt = DatabaseAttemptModel::insert($account, $databaseAttempt);

                // $result->response = $databaseAttempt;
                $result = self::updateDatabase($request, $databaseById, $databaseAttempt);
            }

        }

        return $result;

    }


    private static function updateDatabase($request, $database, $databaseAttempt) {

        $result = new stdClass();
        $result->response = "Failed to update worksheet database data";
        $result->result = false;

        $account = DataComponent::initializeAccount($request);

        $database->name = $request->name;
        $database->status = "Processed";
        DatabaseModel::update($account, $database, $request->websiteId);

        $request->account = [
            "username" => strtolower($request->account["username"])
        ];

        if(is_null($request->status)) {

            $request->status = "Pending";

        }

        $websiteById = WebsiteModel::findOneById($request->websiteId);

        if(!empty($websiteById)) {

            $databaseAccountByUsername = DatabaseAccountModel::findOneByUsernameNotDatabaseId($database->_id, $request->account["username"], $websiteById->_id);

            if(empty($databaseAccountByUsername)) {

                if(!empty($request->account["username"])) {

                    $databaseAccountById = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);

                    if(!empty($databaseAccountById)) {

                        $databaseAccountById->username = $request->account["username"];
                        DatabaseAccountModel::update($account, $databaseAccountById, $websiteById->_id);

                    } else {

                        $databaseAccount = new DatabaseAccount();
                        $databaseAccount->database = [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $databaseAccount->deposit = [
                            "average" => [
                                "amount" => 0.00,
                            ],
                            "first" => [
                                "amount" => 0.00,
                                "timestamp" => ""
                            ],
                            "last" => [
                                "amount" => 0.00,
                                "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00"))
                            ],
                            "total" => [
                                "amount" => 0,
                                "time" => 0
                            ]
                        ];
                        $databaseAccount->games = [];
                        $databaseAccount->sync = [
                            "_id" => "0",
                            "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00"))
                        ];
                        $databaseAccount->withdrawal = [
                            "average" => [
                                "amount" => 0.00,
                            ],
                            "first" => [
                                "amount" => 0.00,
                                "timestamp" => ""
                            ],
                            "last" => [
                                "amount" => 0.00,
                                "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00"))
                            ],
                            "total" => [
                                "amount" => 0,
                                "time" => 0
                            ]
                        ];
                        $databaseAccount->login = [
                            "average" => [
                                "daily" => 0,
                                "monthly" => 0,
                                "weekly" => 0,
                                "yearly" => 0
                            ],
                            "first" => [
                                "timestamp" => ""
                            ],
                            "last" => [
                                "timestamp" => new UTCDateTime(Carbon::createFromFormat("Y-m-d H:i:s", "1970-01-10 00:00:00"))
                            ],
                            "total" => [
                                "amount" => 0
                            ]
                        ];
                        $databaseAccount->reference = "";
                        $databaseAccount->register = [
                            "timestamp" => new UTCDateTime()
                        ];
                        $databaseAccount->username = $request->account["username"];
                        DatabaseAccountModel::insert($account, $databaseAccount, $websiteById->_id);
                    }

                    $playerAttemptByUsername = PlayerAttemptModel::findOneByUsername($account->nucode, $request->account["username"]);

                    if(!empty($playerAttemptByUsername)) {

                        $playerAttemptByUsername->status = self::initializeStatusData($playerAttemptByUsername, $request->status);
                        $playerAttemptByUsername->total += 1;
                        $playerAttemptByUsername->website = self::initializeWebsiteData($playerAttemptByUsername, $websiteById);
                        PlayerAttemptModel::update($account, $playerAttemptByUsername);

                    }

                }

                $databaseAttempt->status = self::initializeStatusData($databaseAttempt, $request->status);
                $databaseAttempt->total += 1;
                $databaseAttempt->website = self::initializeWebsiteData($databaseAttempt, $websiteById);
                DatabaseAttemptModel::update($account, $databaseAttempt);

                if(is_null($request->reference)) {

                    $request->reference = "";

                }

                $databaseLogById = DatabaseLogModel::findOneById($request->log["id"], $websiteById->_id);

                if(!empty($databaseLogById)) {

                    $databaseLogById->status = $request->status;
                    DatabaseLogModel::update($account, $databaseLogById, $websiteById->_id);

                } else {

                    $databaseLog = new DatabaseLog();
                    $databaseLog->database = [
                        "_id" => DataComponent::initializeObjectId($database->_id),
                        "name" => $database->name
                    ];
                    $databaseLog->status = $request->status;
                    $databaseLog->user = [
                        "_id" => DataComponent::initializeObjectId($account->_id),
                        "avatar" => $account->avatar,
                        "name" => $account->name,
                        "username" => $account->username
                    ];
                    $databaseLog->website = [
                        "_id" => DataComponent::initializeObjectId($websiteById->_id),
                        "name" => $websiteById->name
                    ];
                    DatabaseLogModel::insert($account, $databaseLog, $websiteById->_id);

                }

                self::generateReport($account, new UTCDateTime(Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->setMicrosecond(0)), $request->status, $websiteById);

                $result->response = "Worksheet data updated";
                $result->result = true;

            } else {

                $result->response = "Username already exist";

            }

        }

        return $result;

    }

    // public static function processSms($request){
    //     $result = new stdClass();
    //     $result->response = "Failed to Process Broadcast SMS";
    //     $result->result = false;
    //     $total_data = 0;
    //     $data = [];
    //     $limit = 100;
    //     $offset = 0;
    //     $dataQueueSms = [];
    //     $auth = DataComponent::initializeAccount($request);

    //     if($request->website){
    //         $websiteById = WebsiteModel::findOneById($request->website); 

    //         if($request->status == 'Available'){
    //             if($auth->type == "CRM") {

    //                 $resp = DatabaseModel::findListWorksheetCrm($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];
                
    //                 // $count = DatabaseAccountModel::countNewDataCrmTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataCrmTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
        
        
    //             } else if($auth->type == "Telemarketer") {
                    
    //                 $resp = DatabaseModel::findListWorksheetTelemarketer($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    //                 // $count = DatabaseAccountModel::countNewDataTeleTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataTeleTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
        
    //             }
    //             if($total_data == 0) {
    //                 $resp = DatabaseModel::findListWorksheetGroup($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    //                 // $count = DatabaseAccountModel::countNewDataGroupTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataGroupTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
    
    //                 if($total_data == 0) {
    //                     $resp = DatabaseModel::findListWorksheetWebsite("Available", $limit, $offset, $websiteById->_id);
    //                     $total_data = $resp['total_data'];
    //                     $data = $resp['data'];

    //                     // $count = DatabaseAccountModel::countNewDataWorksheetTable('Available', $limit, $websiteById->_id);
    //                     // if(!$count->isEmpty()) {
    //                     //     $total_data = $count[0]->count;
    //                     // }
    //                     // $data = DatabaseAccountModel::findNewDataWorksheetTable("Available", $limit, [], $offset, $websiteById->_id);
    //                 }
    //             }
                
    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "Sms");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type SMS";
    //                     return $result;
    //                 }

    //                 foreach($data as $val){
                        
    //                     $database = DatabaseModel::findOneById($val['_id'],$websiteById->_id);
    //                     // $result->result = true;
    //                     // $result->response = $database;
    //                     // return $result;
    //                     // print_r(json_encode($database));die;
        
    //                     if(!empty($database)) {
    //                         if(!isset($database->lastSmsDate)){
    //                             $database->lastSmsDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastSmsDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_sms', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 3;
    
    //                         if($interval->days >= $interval_setting){
                        
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                            
    //                             //queue sms message
    //                             $dataQueueSms = new SmsQueue();
    //                             $dataQueueSms->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueSms->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueSms->number = $database->contact['phone'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "Sms");
    //                             $dataQueueSms->message = $dataMsg->format;
    //                             $dataQueueSms->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueSms->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueSms->save();
    //                         }
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "SMS Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
    //         }else{
    //             $depositLastTimestamp = new UTCDateTime(Carbon::now()->subDays($request->days));
    //             $count = DatabaseAccountModel::countCrmTable($auth['_id'], $depositLastTimestamp, $limit, $websiteById->_id);
    //             if(!$count->isEmpty()) {
    //                 $total_data = $count[0]->count;
    //             }
    
    //             $data = DatabaseAccountModel::findCrmTable($auth['_id'], $depositLastTimestamp, $limit, [], $offset, $websiteById->_id);

    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "Sms");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type SMS";
    //                     return $result;
    //                 }
    //                 foreach($data as $val){
    
    //                     $database = DatabaseModel::findOneById($val->database[0]['_id']->__toString(),$websiteById->_id);
    //                     // print_r(json_encode($database));die;
        
    //                     if(!empty($database)) {
    //                         if(!isset($database->lastSmsDate)){
    //                             $database->lastSmsDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastSmsDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_sms', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 3;
    
    //                         if($interval->days >= $interval_setting){
                        
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                            
    //                             //queue sms message
    //                             $dataQueueSms = new SmsQueue();
    //                             $dataQueueSms->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueSms->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueSms->number = $database->contact['phone'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "Sms");
    //                             $dataQueueSms->message = $dataMsg->format;
    //                             $dataQueueSms->nucode = $auth->nucode;
    //                             $dataQueueSms->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueSms->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueSms->save();
    //                         }
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "SMS Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
    //         }
    //     }
    //     return $result;
    // }
    
    public static function processSms($request){
        $result = new stdClass();
        $result->response = "Failed to Process Broadcast SMS";
        $result->result = false;
        $dataQueueSms = [];
        $auth = DataComponent::initializeAccount($request);

        if($request->website){
            $websiteById = WebsiteModel::findOneById($request->website); 
            $total_msg = MessageTemplateModel::countByType($auth, "Sms");
                    
            if($total_msg < 1){
                $result->result = false;
                $result->response = "No Message Template for Type SMS";
                return $result;
            }

            foreach($request->ids as $val){
                $database = DatabaseModel::findOneById($val, $websiteById->_id);

                if(!empty($database)) {
                    if(!isset($database->lastSmsDate)){
                        $database->lastSmsDate = "1970-01-01";
                    }

                    $date1 = new \DateTime($database->lastSmsDate);
                    $date2 = new \DateTime(date("Y-m-d"));
                    $interval = $date1->diff($date2);

                    $settings = SettingModel::getSettingByName('interval_sms', $auth->nucode);
                    $interval_setting = isset($settings->value) ? $settings->value : 3;

                    if($interval->days >= $interval_setting){

                        $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
                        if(!empty($databaseLogByDatabaseIdUserId)) {
                            $database->reference = $databaseLogByDatabaseIdUserId->reference;
                        }

                        $database->status = "Reserved";                        

                        if($auth->type == "Telemarketer") {

                            $database->telemarketer = [
                                "_id" => $auth->_id,
                                "avatar" => $auth->avatar,
                                "name" => $auth->name,
                                "username" => $auth->username
                            ];

                        }

                        DatabaseModel::update($auth, $database, $websiteById->_id);

                        $databaseLog = new DatabaseLog();
                        $databaseLog->database = [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $databaseLog->status = "Pending";
                        $databaseLog->user = [
                            "_id" => DataComponent::initializeObjectId($auth->_id),
                            "avatar" => $auth->avatar,
                            "name" => $auth->name,
                            "username" => $auth->username
                        ];
                        $databaseLog->website = [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                    
                        //queue sms message
                        $dataQueueSms = new SmsQueue();
                        $dataQueueSms->nucode = $auth->nucode;
                        $dataQueueSms->website =  [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $dataQueueSms->database =  [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $dataQueueSms->number = $database->contact['phone'];
                        $dataMsg = new MessageTemplateModel($request);
                        $dataMsg = $dataMsg->getRandomMessage($auth, "Sms");
                        $dataQueueSms->message = $dataMsg->format;
                        $dataQueueSms->created = DataComponent::initializeTimestamp($auth);
                        $dataQueueSms->modified = DataComponent::initializeTimestamp($auth);
                        $dataQueueSms->save();
                    }
                }
            }

            $result->result = true;
            $result->response = "SMS Broadcast Processesed";
        }

        return $result;
    }


    // public static function processWA($request){
    //     $result = new stdClass();
    //     $result->response = "Failed to Process Broadcast WA";
    //     $result->result = false;
    //     $total_data = 0;
    //     $data = [];
    //     $limit = 100;
    //     $offset = 0;
    //     $dataQueueWA = [];
    //     $auth = DataComponent::initializeAccount($request);

    //     if($request->website){
    //         $websiteById = WebsiteModel::findOneById($request->website); 

    //         if($request->status == 'Available'){
    //             if($auth->type == "CRM") {

    //                 $resp = DatabaseModel::findListWorksheetCrm($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];
        
    //             } else if($auth->type == "Telemarketer") {
                    
    //                 $resp = DatabaseModel::findListWorksheetTelemarketer($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    
        
    //             }
    //             if($total_data == 0) {
    //                 $resp = DatabaseModel::findListWorksheetGroup($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    
    //                 if($total_data == 0) {
    //                     $resp = DatabaseModel::findListWorksheetWebsite("Available", $limit, $offset, $websiteById->_id);
    //                     $total_data = $resp['total_data'];
    //                     $data = $resp['data'];

    //                 }
    //             }

    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "WA");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type WA";
    //                     return $result;
    //                 }

    //                 foreach($data as $val){
    //                     $database = DatabaseModel::findOneById($val['_id'],$websiteById->_id);
    //                     // print_r(json_encode($database));die;
        
    //                     if(!empty($database)) {
    //                         if(!isset($database->lastWaDate)){
    //                             $database->lastWaDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastWaDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_wa', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 3;
    
    //                         if($interval->days >= $interval_setting){
                        
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                            
    //                             //queue wa message
    //                             $dataQueueWA = new WaQueue();
    //                             $dataQueueWA->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueWA->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueWA->number = $database->contact['phone'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "WA");
    //                             $dataQueueWA->message = $dataMsg->format;
    //                             $dataQueueWA->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueWA->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueWA->save();
    //                         }
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "WA Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
    //         }else{
    //             $depositLastTimestamp = new UTCDateTime(Carbon::now()->subDays($request->days));
    //             $count = DatabaseAccountModel::countCrmTable($auth['_id'], $depositLastTimestamp, $limit, $websiteById->_id);
    //             if(!$count->isEmpty()) {
    //                 $total_data = $count[0]->count;
    //             }
    
    //             $data = DatabaseAccountModel::findCrmTable($auth['_id'], $depositLastTimestamp, $limit, [], $offset, $websiteById->_id);

    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "WA");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type WA";
    //                     return $result;
    //                 }
    //                 foreach($data as $val){
    
    //                     $database = DatabaseModel::findOneById($val->database[0]['_id']->__toString(),$websiteById->_id);
    //                     // print_r(json_encode($database));die;
        
    //                     if(!empty($database)) {
    //                         if(!isset($database->lastWaDate)){
    //                             $database->lastWaDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastWaDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_wa', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 3;
    
    //                         if($interval->days >= $interval_setting){
                        
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             // $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                            
    //                             //queue wa message
    //                             $dataQueueWA = new WaQueue();
    //                             $dataQueueWA->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueWA->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueWA->number = $database->contact['phone'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "WA");
    //                             $dataQueueWA->message = $dataMsg->format;
    //                             $dataQueueWA->nucode = $auth->nucode;
    //                             $dataQueueWA->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueWA->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueWA->save();
    //                         }
                            
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "WA Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
                
    //         }
    //     }
    //     return $result;
    // }

    public static function processWA($request){
        $result = new stdClass();
        $result->response = "Failed to Process Broadcast WA";
        $result->result = false;
        $dataQueueWA = [];
        $auth = DataComponent::initializeAccount($request);

        if($request->website){
            $websiteById = WebsiteModel::findOneById($request->website); 
            //check message template
            $total_msg = MessageTemplateModel::countByType($auth, "WA");
                    
            if($total_msg < 1){
                $result->result = false;
                $result->response = "No Message Template for Type WA";
                return $result;
            }

            foreach($request->ids as $val){
                $database = DatabaseModel::findOneById($val, $websiteById->_id);

                if(!empty($database)) {
                    if(!isset($database->lastWaDate)){
                        $database->lastWaDate = "1970-01-01";
                    }

                    $date1 = new \DateTime($database->lastWaDate);
                    $date2 = new \DateTime(date("Y-m-d"));
                    $interval = $date1->diff($date2);

                    $settings = SettingModel::getSettingByName('interval_wa', $auth->nucode);
                    $interval_setting = isset($settings->value) ? $settings->value : 3;

                    if($interval->days >= $interval_setting){

                        $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
                        if(!empty($databaseLogByDatabaseIdUserId)) {
                            $database->reference = $databaseLogByDatabaseIdUserId->reference;
                        }

                        $database->status = "Reserved";                 
                        if($auth->type == "Telemarketer") {

                            $database->telemarketer = [
                                "_id" => $auth->_id,
                                "avatar" => $auth->avatar,
                                "name" => $auth->name,
                                "username" => $auth->username
                            ];

                        }

                        DatabaseModel::update($auth, $database, $websiteById->_id);

                        $databaseLog = new DatabaseLog();
                        $databaseLog->database = [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $databaseLog->status = "Pending";
                        $databaseLog->user = [
                            "_id" => DataComponent::initializeObjectId($auth->_id),
                            "avatar" => $auth->avatar,
                            "name" => $auth->name,
                            "username" => $auth->username
                        ];
                        $databaseLog->website = [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                    
                        //queue wa message
                        $dataQueueWA = new WaQueue();
                        $dataQueueWA->nucode = $auth->nucode;
                        $dataQueueWA->website =  [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $dataQueueWA->database =  [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $dataQueueWA->number = $database->contact['phone'];
                        $dataMsg = new MessageTemplateModel($request);
                        $dataMsg = $dataMsg->getRandomMessage($auth, "WA");
                        $dataQueueWA->message = $dataMsg->format;
                        $dataQueueWA->created = DataComponent::initializeTimestamp($auth);
                        $dataQueueWA->modified = DataComponent::initializeTimestamp($auth);
                        $dataQueueWA->save();
                    }
                }
            }

            $result->result = true;
            $result->response = "WA Broadcast Processesed";
        }
        return $result;
    }

    // public static function processEmail($request){
    //     $result = new stdClass();
    //     $result->response = "Failed to Process Broadcast Email";
    //     $result->result = false;
    //     $total_data = 0;
    //     $data = [];
    //     $limit = 100;
    //     $offset = 0;
    //     $dataQueueEmail = [];
    //     $auth = DataComponent::initializeAccount($request);

    //     if($request->website){
    //         $websiteById = WebsiteModel::findOneById($request->website); 

    //         if($request->status == 'Available'){
    //             if($auth->type == "CRM") {

    //                 $resp = DatabaseModel::findListWorksheetCrm($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];
                
    //                 // $count = DatabaseAccountModel::countNewDataCrmTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataCrmTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
        
        
    //             } else if($auth->type == "Telemarketer") {
                    
    //                 $resp = DatabaseModel::findListWorksheetTelemarketer($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    //                 // $count = DatabaseAccountModel::countNewDataTeleTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataTeleTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
        
    //             }
    //             if($total_data == 0) {
    //                 $resp = DatabaseModel::findListWorksheetGroup($auth['_id'], "Available", $limit, $offset, $websiteById->_id);
    //                 $total_data = $resp['total_data'];
    //                 $data = $resp['data'];

    //                 // $count = DatabaseAccountModel::countNewDataGroupTable($auth['_id'], 'Available', $limit, $websiteById->_id);
    //                 // if(!$count->isEmpty()) {
    //                 //     $total_data = $count[0]->count;
    //                 // }
    //                 // $data = DatabaseAccountModel::findNewDataGroupTable($auth['_id'], "Available", $limit, [], $offset, $websiteById->_id);
    
    //                 if($total_data == 0) {
    //                     $resp = DatabaseModel::findListWorksheetWebsite("Available", $limit, $offset, $websiteById->_id);
    //                     $total_data = $resp['total_data'];
    //                     $data = $resp['data'];

    //                     // $count = DatabaseAccountModel::countNewDataWorksheetTable('Available', $limit, $websiteById->_id);
    //                     // if(!$count->isEmpty()) {
    //                     //     $total_data = $count[0]->count;
    //                     // }
    //                     // $data = DatabaseAccountModel::findNewDataWorksheetTable("Available", $limit, [], $offset, $websiteById->_id);
    //                 }
    //             }

    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "Email");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type Email";
    //                     return $result;
    //                 }
    //                 foreach($data as $val){
    
    //                     $database = DatabaseModel::findOneById($val['_id'],$websiteById->_id);
        
    //                     if(!empty($database)) {
                            
    //                         if(!isset($database->lastEmailDate)){
    //                             $database->lastEmailDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastEmailDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_email', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 1;
    
    //                         if($interval->days >= $interval_setting){
    
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
    //                             // array_push($numbers, $database->contact['phone']);
                            
                            
                               
    //                             //queue wa message
    //                             $dataQueueEmail = new EmailQueue();
    //                             $dataQueueEmail->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueEmail->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueEmail->email = $database->contact['email'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "Email");
    //                             $dataQueueEmail->subject = $dataMsg->name;
    //                             $dataQueueEmail->body = $dataMsg->format;
    //                             $dataQueueEmail->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueEmail->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueEmail->save();
    //                         }
                            
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "Email Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
    //         }else{
                
    //             $depositLastTimestamp = new UTCDateTime(Carbon::now()->subDays($request->days));
    //             $count = DatabaseAccountModel::countCrmTable($auth['_id'], $depositLastTimestamp, $limit, $websiteById->_id);
                
    //             if(!$count->isEmpty()) {
    //                 $total_data = $count[0]->count;
    //             }
    
    //             $data = DatabaseAccountModel::findCrmTable($auth['_id'], $depositLastTimestamp, $limit, [], $offset, $websiteById->_id);
    //             if($total_data > 0){
    //                 //check message template
    //                 $total_msg = MessageTemplateModel::countByType($auth, "Email");
                    
    //                 if($total_msg < 1){
    //                     $result->result = false;
    //                     $result->response = "No Message Template for Type Email";
    //                     return $result;
    //                 }
    //                 foreach($data as $val){
    
    //                     $database = DatabaseModel::findOneById($val->database[0]['_id']->__toString(),$websiteById->_id);
    //                     // print_r(json_encode($database));die;
        
    //                     if(!empty($database)) {
                            
    //                         if(!isset($database->lastEmailDate)){
    //                             $database->lastEmailDate = "1970-01-01";
    //                         }
    
    //                         $date1 = new \DateTime($database->lastEmailDate);
    //                         $date2 = new \DateTime(date("Y-m-d"));
    //                         $interval = $date1->diff($date2);
    
    //                         $settings = SettingModel::getSettingByName('interval_email', $auth->nucode);
    //                         $interval_setting = isset($settings->value) ? $settings->value : 1;
    
    //                         if($interval->days >= $interval_setting){
    
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
    //                             if(!empty($databaseLogByDatabaseIdUserId)) {
    //                                 $database->reference = $databaseLogByDatabaseIdUserId->reference;
    //                             }
    
    //                             $database->status = "Reserved";                        
    
    //                             if($auth->type == "Telemarketer") {
    
    //                                 $database->telemarketer = [
    //                                     "_id" => $auth->_id,
    //                                     "avatar" => $auth->avatar,
    //                                     "name" => $auth->name,
    //                                     "username" => $auth->username
    //                                 ];
    
    //                             }
    
    //                             DatabaseModel::update($auth, $database, $websiteById->_id);
    
    //                             $databaseAccount = DatabaseAccountModel::findOneByDatabaseId($database->_id, $websiteById->_id);
    
    //                             $databaseLog = new DatabaseLog();
    //                             $databaseLog->database = [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $databaseLog->status = "Pending";
    //                             $databaseLog->user = [
    //                                 "_id" => DataComponent::initializeObjectId($auth->_id),
    //                                 "avatar" => $auth->avatar,
    //                                 "name" => $auth->name,
    //                                 "username" => $auth->username
    //                             ];
    //                             $databaseLog->website = [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
    //                             // array_push($numbers, $database->contact['phone']);
                            
    //                             //queue wa message
    //                             $dataQueueEmail = new EmailQueue();
    //                             $dataQueueEmail->website =  [
    //                                 "_id" => DataComponent::initializeObjectId($websiteById->_id),
    //                                 "name" => $websiteById->name
    //                             ];
    //                             $dataQueueEmail->database =  [
    //                                 "_id" => DataComponent::initializeObjectId($database->_id),
    //                                 "name" => $database->name
    //                             ];
    //                             $dataQueueEmail->email = $database->contact['email'];
    //                             $dataMsg = new MessageTemplateModel($request);
    //                             $dataMsg = $dataMsg->getRandomMessage($auth, "Email");
    //                             $dataQueueEmail->subject = $dataMsg->name;
    //                             $dataQueueEmail->body = $dataMsg->format;
    //                             $dataQueueEmail->nucode = $auth->nucode;
    //                             $dataQueueEmail->created = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueEmail->modified = DataComponent::initializeTimestamp($auth);
    //                             $dataQueueEmail->save();
    //                         }   
    //                     }
    //                 }
    
    //                 $result->result = true;
    //                 $result->response = "Email Broadcast Processesed";
    
    //             }else{
    //                 $result->response = "There is no data to broadcast";
    //             }
                
    //         }
    //     }
    //     return $result;
    // }

    public static function processEmail($request){
        $result = new stdClass();
        $result->response = "Failed to Process Broadcast Email";
        $result->result = false;
        $dataQueueEmail = [];
        $auth = DataComponent::initializeAccount($request);

        if($request->website){

            $websiteById = WebsiteModel::findOneById($request->website); 
            //check message template
            $total_msg = MessageTemplateModel::countByType($auth, "Email");
            if($total_msg < 1){
                $result->result = false;
                $result->response = "No Message Template for Type Email";
                return $result;
            }

            foreach($request->ids as $val){
                $database = DatabaseModel::findOneById($val, $websiteById->_id);
                if(!empty($database)) {
                    if(!isset($database->lastEmailDate)){
                        $database->lastEmailDate = "1970-01-01";
                    }
                    $date1 = new \DateTime($database->lastEmailDate);
                    $date2 = new \DateTime(date("Y-m-d"));
                    $interval = $date1->diff($date2);

                    $settings = SettingModel::getSettingByName('interval_email', $auth->nucode);
                    $interval_setting = isset($settings->value) ? $settings->value : 1;

                    if($interval->days >= $interval_setting){
                        $databaseLogByDatabaseIdUserId = DatabaseLogModel::findLastByDatabaseIdUserId($database->_id, $auth->_id, $websiteById->_id);
                        if(!empty($databaseLogByDatabaseIdUserId)) {
                            $database->reference = $databaseLogByDatabaseIdUserId->reference;
                        }

                        $database->status = "Reserved";                        

                        if($auth->type == "Telemarketer") {

                            $database->telemarketer = [
                                "_id" => $auth->_id,
                                "avatar" => $auth->avatar,
                                "name" => $auth->name,
                                "username" => $auth->username
                            ];

                        }

                        DatabaseModel::update($auth, $database, $websiteById->_id);

                        $databaseLog = new DatabaseLog();
                        $databaseLog->database = [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $databaseLog->status = "Pending";
                        $databaseLog->user = [
                            "_id" => DataComponent::initializeObjectId($auth->_id),
                            "avatar" => $auth->avatar,
                            "name" => $auth->name,
                            "username" => $auth->username
                        ];
                        $databaseLog->website = [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $databaseLog = DatabaseLogModel::insert($auth, $databaseLog, $websiteById->_id);
                        // array_push($numbers, $database->contact['phone']);
                        
                        //queue wa message
                        $dataQueueEmail = new EmailQueue();
                        $dataQueueEmail->nucode = $auth->nucode;
                        $dataQueueEmail->website =  [
                            "_id" => DataComponent::initializeObjectId($websiteById->_id),
                            "name" => $websiteById->name
                        ];
                        $dataQueueEmail->database =  [
                            "_id" => DataComponent::initializeObjectId($database->_id),
                            "name" => $database->name
                        ];
                        $dataQueueEmail->email = $database->contact['email'];
                        $dataMsg = new MessageTemplateModel($request);
                        $dataMsg = $dataMsg->getRandomMessage($auth, "Email");
                        $dataQueueEmail->subject = $dataMsg->name;
                        $dataQueueEmail->body = $dataMsg->format;
                        $dataQueueEmail->created = DataComponent::initializeTimestamp($auth);
                        $dataQueueEmail->modified = DataComponent::initializeTimestamp($auth);
                        $dataQueueEmail->save();
                    }
                }
            }

            $result->result = true;
            $result->response = "Email Broadcast Processesed";
        }
        return $result;
    }



}
