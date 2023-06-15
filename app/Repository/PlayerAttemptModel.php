<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\PlayerAttempt;
use Carbon\Carbon;

class PlayerAttemptModel 
{

    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function findOneById($id, $nucode) 
    {

        return PlayerAttempt::where('_id', $id)->first();

    }


    public function findOneByUsername($nucode, $username) 
    {

        return PlayerAttempt::where('username', $username)
        ->first();
    }


    public function insert($auth, $data) 
    {

        $mytime = Carbon::now();
        
         $arr = [
        "status" => [
            "names" => $data->names,
            "totals" => $data->totals
        ],
        "total" => $data->total,
        "username" => $data->username,
        "website" => [
            "ids" => $data->ids,
            "names" => $data->names,
            "totals" => $data->totals
        ],
        "created" => DataComponent::initializeTimestamp($this->user),
        "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('playerAttempt')
            ->insert($arr);
    }


    public function update($auth, $data) 
    {

        if($auth != null) 
        {

            $data->modified = AuthenticationComponent::validate($auth);

        }

        $mytime = Carbon::now();
         $arr = [
            "status" => [
                "names" => $data->names,
                "totals" => $data->totals
            ],
            "total" => $data->total,
            "username" => $data->username,
            "website" => [
                "ids" => $data->ids,
                "names" => $data->names,
                "totals" => $data->totals
            ],
            // "created" => [
            //     "timestamp" => $data->timestamp,
            //     "user" => [
            //         "_id" => $auth->_id,
            //         "username" => $data->username
            //     ]
            // ],
            "modified" => DataComponent::initializeTimestamp($this->user)
            ];

         return DB::table('playerAttempt')
         ->where('_id', $data->id)->update($arr);

    }


}
