<?php

namespace App\Repository;

use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WebsiteModel
{
    public function getAllWebsite($limit=10, $offset=0)
    {   
        return Website::get()->take($limit)->skip($offset);
    }

    public function addWebsite($data, $auth)
    {

        $mytime = Carbon::now();

        $arr = [
            "api" => [
                "nexus" => [
                    "code" => "",
                    "salt" => "",
                    "url" => ""
                ]
            ],
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "start" => "",
            "status" => $data->status,
            "sync" => $data->sync,
            "created" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ]
        ];

        $websiteByNameNucode = self::findOneByNameNucode($data->name, $data->nucode);

        if(!empty($websiteByNameNucode)) {

            if(!$data->id == $websiteByNameNucode->id) {

                // array_push($validation, false);

                // $data->response = "Website name already exist";

                return false;

            }

        }

        return DB::table('website')
            ->insert($arr);
    }

    public static function deleteWebsite($id)
    {

        return Website::where('_id', $id)->delete();
    }

    public function getWebsiteById($id)
    {

        return Website::where('_id', $id)->first();
    }

    public function updateWebsiteById($data, $auth)
    {
        $mytime = Carbon::now();

        $arr = [
            // "api" => [
            //     "nexus" => [
            //         "code" => "",
            //         "salt" => "",
            //         "url" => ""
            //     ]
            // ],
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "start" => "",
            "status" => $data->status,
            "sync" => $data->sync,
            // "created" => [
            //     "timestamp" => $mytime->toDateTimeString(),
            //     "user" => [
            //         "_id" => $auth->_id,
            //         "username" => $auth->username
            //     ]
            // ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                "user" => [
                    "_id" => $auth->_id,
                    "username" => $auth->username
                ]
            ]
        ];

        return DB::table('website')->where('_id', $data->id)->update($arr);
    }

    public static function findOneById($id) {

        return Website::where([
            ["_id", "=", $id]
        ])->first();

    }

    public static function findOneByNameNucode($name, $nucode) {

        return Website::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();

    }


    // public static function count() {

    //     return Website::where([])->count("_id");

    // }


    // public static function delete($data) {

    //     return $data->delete();

    // }


    // public static function deleteByNucode($nucode) {

    //     return Website::where("nucode", $nucode)->delete();

    // }


    // public static function findAll() {

    //     return Website::where([])->get();

    // }


    // public static function findByNucode($nucode) {

    //     return Website::where([
    //         ["nucode", "=", $nucode]
    //     ])->get();

    // }


    // public static function findByNucodeStatus($nucode, $status) {

    //     return Website::where([
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findByStatus($status) {

    //     return Website::where([
    //         ["status", "=", $status]
    //     ])->get();

    // }


    // public static function findInId($ids) {

    //     return Website::whereIn("_id", $ids)->get();

    // }


    // public static function findByStatusNotApiNexusSaltStart($apiNexusSalt, $start, $status) {

    //     return Website::where([
    //         ["api.nexus.salt", "!=", $apiNexusSalt],
    //         ["start", "!=", $start],
    //         ["status", "=", $status]
    //     ])->get();

    // }


    


    // public static function findOneByIdNucodeStatus($id, $nucode, $status) {

    //     return Website::where([
    //         ["_id", "=", $id],
    //         ["nucode", "=", $nucode],
    //         ["status", "=", $status]
    //     ])->first();

    // }


    


    // public static function findPageNotApiNexusSaltStart($apiNexusSalt, $start, $page, $size) {

    //     return Website::where([
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
