<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseAccountModel;
use App\Repository\DatabaseImportActionModel;
use App\Repository\DatabaseImportModel;
use App\Repository\DatabaseLogModel;
use App\Repository\DatabaseModel;
use App\Repository\UserGroupModel;
use App\Repository\WebsiteModel;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use stdClass;

class DatabaseImportController extends Controller
{
    public function importDatabase(Request $request)
    {
        DataComponent::checkPrivilege($request, "database", "add");

        if (!empty($request->file)) {
            $result = DatabaseService::importData($request);
            $response = [
                'result' => $result->result,
                'response' => $result->response,
            ];
        } else {
            $response = [
                'result' => false,
                'response' => 'failed import database',
            ];
        }
        
        return response()->json($response, 200);
    }


    public function history(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            
            $data = DatabaseImportModel::getAll($request, $limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All History',
                'data' => $data['data'],
                'total_data' => $data['total_data']
            ];
           
        } else {
            $response = $validation;
        }

        return response()->json($response, 200);
    }

    public static function historyDelete(Request $request) {

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        $result = new stdClass();
        $result->response = "Failed to delete database import history data";
        $result->result = false;

        if ($validation->result) {
            DataComponent::checkPrivilege($request, "database", "delete");
            $account = AuthenticationComponent::toUser($request);

            $databaseImportById = DatabaseImportModel::findOneById($request->id, $account->nucode);

            if(!empty($databaseImportById)) {
                $databaseImportAction = DatabaseImportActionModel::findOneByDatabaseImportId($databaseImportById->_id, $account->nucode);

                if(!empty($databaseImportAction)) {

                    foreach($databaseImportAction->phones as $key => $action) {

                        $databaseByContactPhone = DatabaseModel::findOneByContactPhone($action, $databaseImportById->website["_id"]);

                        if(!empty($databaseByContactPhone)) {

                            if($databaseImportAction->inserts[$key]) {

                                DatabaseModel::delete($databaseByContactPhone);
                                DatabaseAccountModel::deleteByDatabaseId($databaseByContactPhone->_id, $databaseImportById->website["_id"]);
                                DatabaseLogModel::deleteByDatabaseId($databaseByContactPhone->_id, $databaseImportById->website["_id"]);

                            } else {

                                if($databaseImportAction->crms[$key]) {

                                    $databaseByContactPhone->crm = [
                                        "_id" => "0",
                                        "avatar" => "",
                                        "name" => "System",
                                        "username" => "system"
                                    ];

                                }

                                if($databaseImportAction->groups[$key]) {

                                    $databaseByContactPhone->group = [
                                        "_id" => "0",
                                        "name" => "System"
                                    ];

                                }

                                if($databaseImportAction->telemarketers[$key]) {

                                    $databaseByContactPhone->telemarketer = [
                                        "_id" => "0",
                                        "avatar" => "",
                                        "name" => "System",
                                        "username" => "system"
                                    ];

                                }

                                DatabaseModel::update($account, $databaseByContactPhone, $databaseImportById->website["_id"]);

                                if($databaseImportAction->accounts[$key]) {

                                    $databaseAccountByDatabaseId = DatabaseAccountModel::findOneByDatabaseId($databaseByContactPhone->_id, $databaseImportById->website["_id"]);

                                    if(!empty($databaseAccountByDatabaseId)) {

                                        $databaseAccountByDatabaseId->deposit = [
                                            "average" => [
                                                "amount" => "0",
                                            ],
                                            "first" => [
                                                "amount" => "0",
                                                "timestamp" => ""
                                            ],
                                            "last" => [
                                                "amount" => "0",
                                                "timestamp" => new UTCDateTime()
                                            ],
                                            "total" => [
                                                "amount" => "0"
                                            ]
                                        ];
                                        $databaseAccountByDatabaseId->games = [];
                                        $databaseAccountByDatabaseId->login = [
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
                                                "timestamp" => new UTCDateTime()
                                            ],
                                            "total" => [
                                                "amount" => "0"
                                            ]
                                        ];
                                        $databaseAccountByDatabaseId->name = "";
                                        $databaseAccountByDatabaseId->reference = "";
                                        $databaseAccountByDatabaseId->register = [
                                            "timestamp" => new UTCDateTime()
                                        ];
                                        $databaseAccountByDatabaseId->username = "";
                                        $databaseAccountByDatabaseId->withdrawal = [
                                            "average" => [
                                                "amount" => "0",
                                            ],
                                            "first" => [
                                                "amount" => "0",
                                                "timestamp" => ""
                                            ],
                                            "last" => [
                                                "amount" => "0",
                                                "timestamp" => new UTCDateTime()
                                            ],
                                            "total" => [
                                                "amount" => "0"
                                            ]
                                        ];
                                        DatabaseAccountModel::update($account, $databaseAccountByDatabaseId, $databaseImportById->website["_id"]);

                                    }

                                }

                            }

                        }

                    }

                }

                $databaseImportById->status = "Deleted";
                DatabaseImportModel::update($account, $databaseImportById);

                $result->response = "All database import history data deleted";
                $result->result = true;
                
            }

            return $result;
        }else{
            $response = $validation;
            return $response;
        }

        

    }



}
