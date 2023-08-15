<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Database;
use DB;


class DatabaseModel {


    public static function getAll($request){

        $account = AuthenticationComponent::toUser($request);

        $database = new Database();
        $database->setTable("database_" . $request->website);
        $database = $database->orderBy('created.timestamp', 'desc');

        $response = [
            "data" => null,
            "total_data" => 0
        ];
        
        if(!empty($request->name)){
            $database = $database->where('name', $request->name);
        }

        if(!empty($request->phone)){
            $database = $database->where('phone', $request->phone);
        }
        
        $recordsTotal = $database->count("_id");
        $data = $database->take($request->limit)->skip($request->offset)->get();        

        $response['data'] = $data;
        $response['total_data'] = $recordsTotal;

        return $response;
    }

    public static function delete($data) {

        return $data->delete();

    }


    public static function findOneById($id, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByContactPhone($contactPhone, $websiteId) {

        // $database = new Database();
        // $database->setTable("database_" . $websiteId);

        // return $database->where([
        //     ["contact.phone", "=", $contactPhone]
        // ])->first();

        return DB::table("database_".$websiteId)->where('_id', $contactPhone)->first();

    }


    public static function findOneLikeName($name, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["name", "LIKE", "%" . $name . "%"],
        ])->orderBy("name", "DESC")->first();

    }


    public static function findOneWorksheetCrm($crmId, $status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", $crmId],
            ["status", "=", $status]
        ])->first();

    }

    public static function findListWorksheetCrm($crmId, $status, $limit, $offset, $websiteId) {
        $database = new Database();
        $database->setTable("database_" . $websiteId);

        $data = $database->where([
                            ["crm.username", $crmId],
                            ["status", $status]
                        ])
                        ->take($limit)
                        ->skip($offset)
                        ->orderBy('created.timestamp', 'desc')
                        ->get();
        
        $total_data = $database->where([
                            ["crm.username", "=", $crmId],
                            ["status", "=", $status]
                        ])
                        ->count();

        return ["data" => $data,
                "total_data" => $total_data];

    }


    public static function findOneWorksheetGroup($groupId, $status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["group._id", "=", $groupId],
            ["status", "=", $status],
            ["telemarketer._id", "=", "0"]
        ])->first();

    }
    public static function findListWorksheetGroup($groupId, $status, $limit, $offset, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        $data = $database->where([
                    ["crm._id", "=", "0"],
                    ["group._id", "=", $groupId],
                    ["status", "=", $status],
                    ["telemarketer._id", "=", "0"]
                ])
                ->take($limit)
                ->skip($offset)
                ->orderBy('created.timestamp', 'desc')
                ->get();
        
        $total_data = $database->where([
                    ["crm._id", "=", "0"],
                    ["group._id", "=", $groupId],
                    ["status", "=", $status],
                    ["telemarketer._id", "=", "0"]
                ])
                ->count();

        return ["data" => $data,
                "total_data" => $total_data];

    }


    public static function findOneWorksheetTelemarketer($status, $telemarketerId, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["status", "=", $status],
            ["telemarketer._id", "=", $telemarketerId]
        ])->first();

    }
    
    public static function findListWorksheetTelemarketer($telemarketerId, $status, $limit, $offset, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);
        $data = $database->where([
                            ["crm._id", "=", "0"],
                            ["status", "=", $status],
                            ["telemarketer.username", "=", $telemarketerId]
                        ])
                        ->take($limit)
                        ->skip($offset)
                        ->orderBy('created.timestamp', 'desc')
                        ->get();
        
        $total_data = $database->where([
                            ["crm._id", "=", "0"],
                            ["status", "=", $status],
                            ["telemarketer.username", "=", $telemarketerId]
                        ])
                        ->count();
            

        return ["data" => $data, "total_data" => $total_data];

    }


    public static function findOneWorksheetWebsite($status, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        return $database->where([
            ["crm._id", "=", "0"],
            ["status", "=", $status],
            ["telemarketer._id", "=", "0"]
        ])->first();

    }
    
    public static function findListWorksheetWebsite($status, $limit, $offset, $websiteId) {

        $database = new Database();
        $database->setTable("database_" . $websiteId);

        $data = $database->where([
                                ["crm._id", "=", "0"],
                                ["status", "=", $status],
                                ["telemarketer._id", "=", "0"]
                            ])
                            ->take($limit)
                            ->skip($offset)
                            ->orderBy('created.timestamp', 'desc')
                            ->get();

        $total_data = $database->where([
                            ["crm._id", "=", "0"],
                            ["status", "=", $status],
                            ["telemarketer._id", "=", "0"]
                        ])
                        ->count();

        return ["data" => $data, "total_data" => $total_data];

    }


    public static function insert($account, $data, $websiteId) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->setTable("database_" . $websiteId);

        $data->save();

        return $data;

    }


    public static function update($account, $data, $websiteId) {

        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }

        $data->setTable("database_" . $websiteId);

        return $data->save();

    }


}
