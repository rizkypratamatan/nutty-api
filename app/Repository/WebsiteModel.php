<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use MongoDB\BSON\UTCDateTime;

class WebsiteModel
{

    public function getAllWebsite($nucode, $limit=10, $offset=0, $filter)
    {   
        $resp = [
            'data' => null,
            'total_data' => 0
        ];

        if($nucode == 'system'){
            $data = Website::take($limit)->skip($offset);
            $counData = new Website();
        }else{
            $data = Website::take($limit)->skip($offset)->where('nucode', $nucode);
            $counData = Website::where('nucode', $nucode);
        }

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

    public function addWebsite($request)
    {
        $account = AuthenticationComponent::toUser($request);

        if($account->nucode == "system"){
            $nucode = $request->nucode;
        }else{
            $nucode = $account->nucode;
        }

        $data = new Website();
        $data->api = [
                        "nexus" => [
                            "code" => "",
                            "salt" => "",
                            "url" => ""
                        ]
                    ];
        $data->description = $request->description;
        $data->name = $request->name;
        $data->nucode = $nucode;
        $data->start = new UTCDateTime(Carbon::createFromFormat("Y/m/d H:i:s", "1970/01/10 00:00:00"));
        $data->status = $request->status;
        $data->sync = "NoSync";
        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = DataComponent::initializeTimestamp($account);

        $websiteByNameNucode = self::findOneByNameNucode($request->name, $request->nucode);

        if(!empty($websiteByNameNucode)) {

            if(!$request->id == $websiteByNameNucode->id) {

                // array_push($validation, false);

                // $data->response = "Website name already exist";

                return false;

            }

        }

        $data->save();

        return $data;
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

    public function updateWebsiteById($request)
    {
        $account = AuthenticationComponent::toUser($request);
        $data = Website::find($request->id);

        $data->description = $request->description;
        $data->name = $request->name;
        $data->nucode = $request->nucode;
        $data->start = "";
        $data->status = $request->status;
        $data->modified = DataComponent::initializeTimestamp($account);
        $data->save();

        //update website in user group
        // DB::table("userGroup")
        //     ->pull("websites", ['_id' => $data->id]);

        // DB::table("userGroup")
        //     ->where('nestedObject.id','=',$data->id)->update(['nestedObject.$.fieldName'=>"newDate"]);
        //     ->where("websites", "elemMatch", ["_id" => $data->id])
        //     ->push("websites", $update->toArray());

        return $data;
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
