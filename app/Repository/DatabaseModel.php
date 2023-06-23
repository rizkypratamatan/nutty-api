<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Database;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseModel
{
    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function getDatabase($website_id, $limit=10, $offset=0)
    {   
        return DB::table("database_".$website_id)->get()->take($limit)->skip($offset);
    }

    public function addDatabase($data, $auth)
    {

        $mytime = Carbon::now();

        $arr = [
            "city" => $data->city,
            "contact" => [
                "email" => $data->email,
                "line" => $data->line,
                "michat" => $data->michat,
                "phone" => $data->phone,
                "telegram" => $data->telegram,
                "wechat" => $data->wechat,
                "whatsapp" => $data->whatsapp
            ],
            "country" => $data->country,
            "crm" => [
                "_id" => $data->_id,
                "avatar" => $data->avatar,
                "name" => $data->name,
                "username" => $data->username
            ],
            "gender" => $data->gender,
            "group" => [
                "_id" => $auth->_id,
                "name" => $auth->username
            ],
            "import" => [
                "_id" => $data->_id,
                "file" => $data->file
            ],
            "language" => $data->language,
            "name" => $data->name,
            "reference" => $data->reference,
            "state" => $data->state,
            "status" => $data->status,
            "street" => $data->street,
            "telemarketer" => [
                "_id" => $data->_id,
                "avatar" => $data->avatar,
                "name" => $data->name,
                "username" => $data->username
            ],
            "zip" => $data->zip,
            'created' => DataComponent::initializeTimestamp($this->user),
            'modified' => DataComponent::initializeTimestamp($this->user)
        ];

        // $websiteByNameNucode = self::findOneByNameNucode($data->name, $data->nucode);

        // if(!empty($websiteByNameNucode)) {

        //     if(!$data->id == $websiteByNameNucode->id) {

        //         // array_push($validation, false);

        //         // $data->response = "Website name already exist";

        //         return false;

        //     }

        // }

        return DB::table("database_".$data->website_id)
            ->insert($arr);
    }

    public static function deleteDatabase($website_id, $id)
    {

        return DB::table("database_".$website_id)->where('_id', $id)->delete();
    }

    public function getDatabaseById($website_id, $id)
    {

        return DB::table("database_".$website_id)->where('_id', $id)->first();
    }

    public function updateDatabaseById($data)
    {
        // print_r($data->all());die;
        // {
        //     "city": null,
        //     "contact": {},
        //     "country": null,
        //     "crm": {},
        //     "gender": null,
        //     "group": {},
        //     "import": {},
        //     "language": null,
        //     "name": "Tes",
        //     "reference": null,
        //     "state": null,
        //     "status": null,
        //     "street": null,
        //     "telemarketer": {},
        //     "zip": null,
        //     "id": "64819374b50fdd726a013b72",
        $arr = [
            "city" => $data->city,
            "contact" => [
                "email" => !empty($data->contact['email'])?$data->contact['email']:"",
                "line" => !empty($data->contact['line'])?$data->contact['line']:"",
                "michat" => !empty($data->contact['michat'])?$data->contact['michat']:"",
                "phone" => !empty($data->contact['phone'])?$data->contact['phone']:"",
                "telegram" => !empty($data->contact['telegram'])?$data->contact['telegram']:"",
                "wechat" => !empty($data->contact['wechat'])?$data->contact['wechat']:"",
                "whatsapp" => !empty($data->contact['whatsapp'])?$data->contact['whatsapp']:""
            ],
            "country" => $data->country,
            // "crm" => [
            //     "_id" => "",
            //     "avatar" => "",
            //     "name" => "",
            //     "username" => ""
            // ],
            "gender" => $data->gender,
            // "group" => [
            //     "_id" => $auth->_id,
            //     "name" => $auth->username
            // ],
            "import" => [
                "_id" => "",
                "file" => ""
            ],
            "language" => $data->language,
            "name" => $data->name,
            "reference" => $data->reference,
            "state" => $data->state,
            "status" => $data->status,
            "street" => $data->street,
            "telemarketer" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => ""
            ],
            "zip" => $data->zip,
            // "created" => [
            //     "timestamp" => $mytime->toDateTimeString(),
            //     "user" => [
            //         "_id" => $auth->_id,
            //         "username" => $auth->username
            //     ]
            // ],
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table("database_".$data->website_id)->where('_id', $data->id)->update($arr);
    }

    public static function findOneById($id) 
    {

        return Database::where([
            ["_id", "=", $id]
        ])->first();

    }

    public static function findOneByNameNucode($name, $nucode) 
    {

        return Database::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();

    }

    public static function findOneByContactPhone($contactPhone, $websiteId) 
    {

        // $database = new Database();
        // $database->setTable("database_" . $websiteId);

        // return $database->where([
        //     ["contact.phone", "=", $contactPhone]
        // ])->first();

        return DB::table("database_".$websiteId)->where('_id', $contactPhone)->first();

    }


    // private static function importAdditionalData($account, $action, $database, $databaseAccount, $website) 
    // {

    //     if(!empty($databaseAccount)) {

    //         if($databaseAccount->database["_id"] != "0" && $database->_id != $databaseAccount->database["_id"]) {

    //             $databaseById = DatabaseRepository::findOneById($databaseAccount->database["_id"], $website->_id);

    //             if(!empty($databaseById)) {

    //                 DatabaseRepository::delete($databaseById);

    //             }

    //             $action["accounts"][count($action["phones"]) - 1] = true;

    //         }

    //         $databaseAccount->database = [
    //             "_id" => DataComponent::initializeObjectId($database->_id),
    //             "name" => $database->name
    //         ];

    //         if(empty($databaseAccount->_id)) {

    //             try {

    //                 DatabaseAccountRepository::insert($account, $databaseAccount, $website->_id);

    //             } catch(Exception $exception) {

    //                 if($exception->getCode() == 11000) {

    //                     self::replaceAccount($account, $database, $databaseAccount, $website);

    //                 }

    //             }

    //         } else {

    //             try {

    //                 DatabaseAccountRepository::update($account, $databaseAccount, $website->_id);

    //             } catch(Exception $exception) {

    //                 if($exception->getCode() == 11000) {

    //                     self::replaceAccount($account, $database, $databaseAccount, $website);

    //                 }

    //             }

    //         }

    //     }

    //     try {

    //         $databaseAttempt = new DatabaseAttempt();
    //         $databaseAttempt->contact = $database->contact;
    //         $databaseAttempt->total = 0;
    //         $databaseAttempt->website = [
    //             "ids" => [],
    //             "names" => [],
    //             "totals" => []
    //         ];
    //         DatabaseAttemptModel::insert($account, $databaseAttempt);

    //     } catch(Exception $exception) {

    //         if($exception->getCode() != 11000) {

    //             Log::error($exception->getMessage());

    //         }

    //     }

    //     return $action;

    // }

    // public static function count() {

    //     return Database::where([])->count("_id");

    // }


    // public static function delete($data) {

    //     return $data->delete();

    // }


    // public static function deleteByNucode($nucode) {

    //     return Database::where("nucode", $nucode)->delete();

    // }


    // public static function findAll() {

    //     return Database::where([])->get();

    // }


    // public static function findByNucode($nucode) {

    //     return Database::where([
    //         ["nucode", "=", $nucode]
    //     ])->get();

    // }


    // public static function findByNucodeStatus($nucode, $status) {

    //     return Database::where([
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findByStatus($status) {

    //     return Database::where([
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findInId($ids) {

    //     return Database::whereIn("_id", $ids)->get();

    // }


    // public static function findByStatusNotApiNexusSaltStart($apiNexusSalt, $start, $status) {

    //     return Database::where([
    //         ["api.nexus.salt", "!=", $apiNexusSalt],
    //         ["start", "!=", $start],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    


    // public static function findOneByIdNucodeStatus($id, $nucode, $status) {

    //     return Database::where([
    //         ["_id", "=", $id],
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->first();

    // }


    


    // public static function findPageNotApiNexusSaltStart($apiNexusSalt, $start, $page, $size) {

    //     return Database::where([
    //         ["api.nexus.salt", "!=", $apiNexusSalt],
    //         ["start", "!=", $start]
    //     ])->forPage($page, $size)->get();

    // }


    // public static function insert($account, $data) {

    //     $data->created = DataComponent::initializeTimestamp($account);
    //     $data->modified = $data->created;

    //     $data->save();

    //     return $data;

    // }


    // public static function update($account, $data) {

    //     if($account != null) {

    //         $data->modified = DataComponent::initializeTimestamp($account);

    //     }

    //     return $data->save();

    // }
}
