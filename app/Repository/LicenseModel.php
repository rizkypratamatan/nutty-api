<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Models\License;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LicenseModel
{
    public function getLicense($auth, $limit=10, $offset=0, $filter = [])
    {
        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $data = DB::table("license" . $auth->_id)->take($limit)->skip($offset);
        $countData = DB::table("license" . $auth->_id);

        if (!empty($filter['nucode'])) {
            $data = $data->where('nucode', 'LIKE', "%" . $filter['nucode'] . "%");
            $countData = $countData->where('nucode', 'LIKE', "%" . $filter['nucode'] . "%");
        }

        $data = $data->orderBy('_id', 'DESC')->get();
        $counData = $countData->count();

        $response = [
            "data" => $data,
            "total_data" => $counData
        ];

        return $response;
    }

    public function getLicenseById($id)
    {

        return License::where('_id', $id)->first();
    }

    public static function findOneByNucode($nucode) 
    {

        return License::where([
            ["nucode", "=", $nucode]
        ])->first();

    }

    public static function delete($data) {

        return $data->delete();

    }


    public static function findOneById($id) {

        return License::where([
            ["_id", "=", $id]
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
