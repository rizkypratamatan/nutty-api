<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WebsiteModel
{
    protected $user;
    protected $request;

    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function getAllWebsite($limit=10, $offset=0, $filter)
    {   
        $resp = [
            'data' => null,
            'total_data' => 0
        ];

        $data = Website::take($limit)->skip($offset);
        $counData = new Website();

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
            $counData = $counData->where('name', 'LIKE', $filter['name']."%");
        }
        if(!empty($filter['nucode'])){
            $data = $data->where('nucode', $filter['nucode']);
            $counData = $counData->where('nucode', $filter['nucode']);
        }
        if(!empty($filter['type'])){
            $data = $data->where('type', $filter['type']);
            $counData = $counData->where('type', $filter['type']);
        }
        if(!empty($filter['status'])){
            $data = $data->where('status', $filter['status']);
            $counData = $data->where('status', $filter['status']);
        }
        $data = $data->get();
        $counData = $counData->count();

        $resp['data'] = $data;
        $resp['total_data'] = $counData;

        return $resp;
    }

    public function addWebsite($data)
    {

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
            "created" => DataComponent::initializeTimestamp($this->user),
            "modified" => DataComponent::initializeTimestamp($this->user)
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
        DB::table("userGroup")
            ->pull("websites", ['_id' => $id]);

        return Website::where('_id', $id)->delete();
    }

    public function getWebsiteById($id)
    {

        return Website::where('_id', $id)->first();
    }

    public function updateWebsiteById($data)
    {
        $arr = [
            "description" => $data->description,
            "name" => $data->name,
            "nucode" => $data->nucode,
            "start" => "",
            "status" => $data->status,
            "sync" => $data->sync,
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        DB::table('website')->where('_id', $data->id)->update($arr);
        $update = Website::where('_id', $data->id)->first();

        //update website in user group
        // DB::table("userGroup")
        //     ->pull("websites", ['_id' => $data->id]);

        // DB::table("userGroup")
        //     ->where('nestedObject.id','=',$data->id)->update(['nestedObject.$.fieldName'=>"newDate"]);
        //     ->where("websites", "elemMatch", ["_id" => $data->id])
        //     ->push("websites", $update->toArray());

        return $update;
    }

    public static function findByStatusNotApiNexusSaltStart($apiNexusSalt, $start, $status) 
    {

        return Website::where([
            ["api.nexus.salt", "!=", $apiNexusSalt],
            ["start", "!=", $start],
            ["status", "=", $status]
        ])->get();

    }

    public static function count() {

        return Website::where([])->count("_id");

    }

    public static function delete($data) {

        return $data->delete();

    }

    public static function deleteByNucode($nucode) {

        return Website::where("nucode", $nucode)->delete();

    }


    public static function findAll() {

        return Website::where([])->get();

    }


    public static function findByNucode($nucode) {

        return Website::where([
            ["nucode", "=", $nucode]
        ])->get();

    }


    public static function findByNucodeStatus($nucode, $status) {

        return Website::where([
            ["nucode", "=", $nucode],
            ["status", "=", $status]
        ])->get();

    }


    public static function findByStatus($status) {

        return Website::where([
            ["status", "=", $status]
        ])->get();

    }


    public static function findIdAll() {

        return Website::where([])->pluck("_id")->toArray();

    }


    public static function findInId($ids) {

        return Website::whereIn("_id", $ids)->get();

    }


    public static function findBySyncNotApiNexusSaltStart($apiNexusSalt, $start, $sync) {

        return Website::where([
            ["api.nexus.salt", "!=", $apiNexusSalt],
            ["start", "!=", $start],
            ["sync", "=", $sync]
        ])->get();

    }


    public static function findOneById($id) {

        return Website::where([
            ["_id", "=", $id]
        ])->first();

    }


    public static function findOneByIdNucodeStatus($id, $nucode, $status) {

        return Website::where([
            ["_id", "=", $id],
            ["nucode", "=", $nucode],
            ["status", "=", $status]
        ])->first();

    }


    public static function findOneByNameNucode($name, $nucode) {

        return Website::where([
            ["name", "=", $name],
            ["nucode", "=", $nucode]
        ])->first();

    }


    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;

    }


    public static function update($account, $data) {
        if($account != null) {

            $data->modified = DataComponent::initializeTimestamp($account);

        }
        return $data->save();
    }
}
