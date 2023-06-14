<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;


class ReportModel
{

    protected $user;
    protected $request;

    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    // public static function countByUserIdBetweenDate($endDate, $nucode, $startDate, $userId) 
    // {

    //     $report = new Report();
    //     $report->setTable("report_" . $nucode);

    //     return $report->where([
    //         ["date", ">=", $startDate],
    //         ["date", "<=", $endDate],
    //         ["user._id", "=", $userId]
    //     ])->count("_id");

    // }


    public static function deleteReport($id)
    {

        return Report::where('_id', $id)->delete();
    }


    // public static function findOneByDateUserId($date, $nucode, $userId) 
    // {

    //     $report = new Report();
    //     $report->setTable("report_" . $nucode);

    //     return $report->where([
    //         ["date", "=", $date],
    //         ["user._id", "=", $userId]
    //     ])->first();

    // }


    // public static function findOneByUserId($nucode, $userId) 
    // {

    //     $report = new Report();
    //     $report->setTable("report_" . $nucode);

    //     return $report->where([
    //         ["user._id", "=", $userId]
    //     ])->first();

    // }


    // public static function findByUserIdBetweenDate($endDate, $length, $nucode, $page, $startDate, $userId) 
    // {

    //     $report = new Report();
    //     $report->setTable("report_" . $nucode);

    //     return $report->where([
    //         ["date", ">=", $startDate],
    //         ["date", "<=", $endDate],
    //         ["user._id", "=", $userId]
    //     ])->forPage($page, $length)->get();

    // }


    // public static function findRawTable($date, $name, $nucode, $username) 
    // {

    //     $report = new Report();
    //     $report->setTable("report_" . $nucode);

    //     return $report->raw(function($collection) use ($date, $name, $nucode, $username) {

    //         $query = [];

    //         if(!is_null($date)) {

    //             $date = explode(" to ", $date);

    //             if(count($date) == 1) {

    //                 array_push($query, [
    //                     '$match' => [
    //                         "date" => new UTCDateTime(Carbon::parse($date[0])->format("U") * 1000)
    //                     ]
    //                 ]);

    //             } else if(count($date) == 2) {

    //                 array_push($query, [
    //                     '$match' => [
    //                         "date" => [
    //                             '$gte' => new UTCDateTime(Carbon::parse($date[0])->format("U") * 1000),
    //                             '$lte' => new UTCDateTime(Carbon::parse($date[1])->format("U") * 1000)
    //                         ]
    //                     ]
    //                 ]);

    //             }

    //         }

    //         if(!is_null($name)) {

    //             array_push($query, [
    //                 '$match' => [
    //                     "user.name" => new Regex($name)
    //                 ]
    //             ]);

    //         }

    //         if(!is_null($username)) {

    //             array_push($query, [
    //                 '$match' => [
    //                     "user.username" => new Regex($username)
    //                 ]
    //             ]);

    //         }

    //         array_push($query, [
    //             '$group' => [
    //                 "_id" => '$user._id',
    //                 "date" => [
    //                     '$push' => '$date'
    //                 ],
    //                 "status" => [
    //                     '$push' => '$status'
    //                 ],
    //                 "total" => [
    //                     '$sum' => '$total'
    //                 ],
    //                 "user" => [
    //                     '$push' => '$user'
    //                 ],
    //                 "website" => [
    //                     '$push' => '$website'
    //                 ]
    //             ]
    //         ]);

    //         return $collection->aggregate($query, ["allowDiskUse" => true]);

    //     });

    // }


    // public static function insert($account, $data) 
    // {

    //     $data->created = DataComponent::initializeTimestamp($account);
    //     $data->modified = $data->created;

    //     $data->setTable("report_" . $account->nucode);

    //     $data->save();

    //     return $data;

    // }


    // public static function update($account, $data) 
    // {

    //     if($account != null) {

    //         $data->modified = DataComponent::initializeTimestamp($account);

    //     }

    //     $data->setTable("report_" . $account->nucode);

    //     return $data->save();

    // }

    public function userReport($id)
    {

        return User::where('_id', 'username', $id)->first();
    }

    public function addReport($data)
    {

        $mytime = Carbon::now();

        $arr = [
            'date' => $mytime->toDateTimeString(),
            'status' => [
                'names' => $data->names,
                'totals' => $data->totals
            ],
            // 'total' => 0,
            'user' => [
                // '_id' => '0',
                'avatar' => '',
                // 'name' => 'System',
                // 'username' => 'system'
            ],
            'website' => [
                'ids' => $data->ids,
                'names' => $data->names,
                'totals' => $data->totals
            ],
            'created' => DataComponent::initializeTimestamp($this->user),
            'modified' => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('report')
            ->insert($arr);
    }

    public function updateReport($data)
    {
        $mytime = Carbon::now();

        $arr = [
            'date' => $mytime->toDateTimeString(),
            'status' => [
                'names' => $data->names,
                'totals' => $data->totals
            ],
            // 'total' => 0,
            'user' => [
                // '_id' => '0',
                'avatar' => '',
                // 'name' => 'System',
                // 'username' => 'system'
            ],
            'website' => [
                'ids' => $data->ids,
                'names' => $data->names,
                'totals' => $data->totals
            ],
            // 'created' => [
            //     'timestamp' => $mytime->toDateTimeString(),
            //     'user' => [
            //         '_id' => '0',
            //         'username' => 'System'
            //     ]
            // ],
            'modified' => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('report')
            ->where('_id', $data->id)->update($arr);
    }

    public function getReportById($id)
    {

        return Report::where('_id', $id)->first();
    }


}
