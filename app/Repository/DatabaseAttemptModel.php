<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\DatabaseImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseAttemptModel
{

    public function insert($data, $auth)
    {

        $mytime = Carbon::now();

        $arr = [
            "contact" => [
                "email" => $data->email,
                "line" => $data->line,
                "michat" => $data->michat,
                "phone" => $data->phone,
                "telegram" => $data->telegram,
                "wechat" => $data->wechat,
                "whatsapp" => $data->whatsapp
            ],
            "status" => [
                "names" => "",
                "totals" => ""
            ],
            "total" => 0,
            "website" => [
                "ids" => "",
                "names" => "",
                "totals" => ""
            ],
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


        return DB::table('databaseAttempt')
            ->insert($arr);
    }

    public static function initializeData($id) 
    {

        return DatabaseImport::where([
            ["_id", "=", $id]
        ])->first();

    }

    
}
