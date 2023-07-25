<?php

namespace App\Repository;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserLogModel
{

    public function insertToLog($data, $type, $authentication=null)
    {
        $mytime = Carbon::now();

        if($authentication == null){
            $authentication = base64_encode(Hash::make($data['_id'] . $data['username'] . $mytime->format("Y-m-dTH:i:s.u")));
        }
        
        $dataLog = [
            "authentication" => $authentication,
            // "agent" => [
            //     "browser" => [
            //         "manufacturer" => "",
            //         "name" => "",
            //         "renderingEngine" => "",
            //         "version" => ""
            //     ],
            //     "device" => [
            //         "os" => "",
            //         "manufacturer" => "",
            //         "type" => ""
            //     ],
            //     "ip" => ""
            // ],
            "description" => "Website",
            // "nucode" => "",
            // "target" => [
            //     "_id" => "0",
            //     "name" => "System"
            // ],
            "type" => $type,
            "user" => [
                "_id" => $data["id"],
                "username" => $data["username"]
            ],
            "created" => [
                "timestamp" => $mytime->toDateTimeString(),
                // "user" => [
                //     "_id" => "0",
                //     "username" => "System"
                // ]
            ],
            "modified" => [
                "timestamp" => $mytime->toDateTimeString(),
                // "user" => [
                //     "_id" => "0",
                //     "username" => "System"
                // ]
            ]
        ];

        DB::table('userLog')->insert($dataLog);

        return $authentication;
    }

    public static function findOneByAuthentication($authentication) {

        return UserLog::where([
            ["authentication", "=", $authentication]
        ])->orderBy("created.timestamp", "DESC")->first();

    }


    public static function findOneByAuthenticationInType($authentication, $types) {

        return UserLog::where([
            ["authentication", "=", $authentication]
        ])
        ->whereIn("type", $types)
        ->orderBy("created.timestamp", "DESC")
        ->first();

    }


    public static function findOneByUserIdInType($userId, $types) {

        return UserLog::where([
            ["user._id", "=", $userId]
        ])->whereIn("type", $types)->orderBy("created.timestamp", "DESC")->first();

    }

    public static function delete($data) {

        return $data->delete();

    }


    public static function deleteByNucode($nucode) {

        return UserLog::where("nucode", $nucode)->delete();

    }

    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;

    }
}
