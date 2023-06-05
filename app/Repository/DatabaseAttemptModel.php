<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\DatabaseAttempt;
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

    public function update($auth,$data)
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
            // "created" => [
            //     "timestamp" => "",
            //     "user" => [
            //         "_id" => "0",
            //         "username" => "System"
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

        return DB::table('databaseAttempt')
            ->where('_id', $data->id)->update($arr);

    }

    public static function findOneById($id, $nucode) 
    {

        $databaseAttempt = new DatabaseAttempt();
        $databaseAttempt->setTable("databaseAttempt_" . $nucode);

        return $databaseAttempt->where([
            ["_id", "=", $id]
        ])->first();

    }

    
}
