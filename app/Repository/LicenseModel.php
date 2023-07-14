<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\License;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LicenseModel
{
    protected $service;
    protected $user;
    protected $request;

    public function __construct(Request $request)
    {   
        // $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public static function deleteLicense($id)
    {

        // return License::where('_id', $id)->delete();

        return DB::table("license")->where("_id", $id)->delete();
    }

    public function updateLicense($data)
    {
        $mytime = Carbon::now();

        $arr = [
            'nucode' => $data->nucode,
            'package' => [
                'expired' => $data->expired,
                'payment' => [
                    'last' => $data->last,
                    'next' => $data->next
                ],
                'start' => $data->start,
                'status' => $data->status,
                'trial' => $data->trial
            ],
            'user' => [
                'primary' => [
                    // '_id' => '0',
                    'avatar' => $data->avatar,
                    'name' => $data->name,
                    'username' => $data->username
                ],
                'total' => 0
            ],
            // 'created' => [
            //     'timestamp' => $mytime->toDateTimeString(),
            //     'user' => [
            //         '_id' => '0',
            //         'username' => 'System'
            //     ]
            // ],
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('user')
            ->where('_id', $data->id)->update($arr);
    }

    public static function addLicense($data, $account)
    {

        $arr = [
            'nucode' => $data->nucode,
            'package' => [
                'expired' => $data->expired,
                'payment' => [
                    'last' => $data->last,
                    'next' => $data->next
                ],
                'start' => $data->start,
                'status' => $data->status,
                'trial' => $data->trial
            ],
            'user' => [
                'primary' => [
                    // '_id' => '0',
                    'avatar' => $data->avatar,
                    'name' => $data->name,
                    'username' => $data->username
                ],
                'total' => 0
            ],
            "created" => DataComponent::initializeTimestamp($account),
            "modified" => DataComponent::initializeTimestamp($account)
        ];

        return DB::table("license".$account->_id)->insert($arr);
    }

    public function getLicense($auth, $limit=10, $offset=0, $filter = [])
    
    {
        // $user = AuthenticationComponent::toUser($this->request);

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $data = License::take($limit)->skip($offset);
        $countData = new License();

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

    public static function getLicenseById($id, $account)
    {

        // return License::where('_id', $id)->first();
        return DB::table("license".$account->_id)
                    ->where("_id", $id)
                    ->first();
    }

    public static function findOneByNucode($nucode) 
    {

        return License::where([
            ["nucode", "=", $nucode]
        ])->first();

    }

    public static function insert($account, $data) {

        $data->created = DataComponent::initializeTimestamp($account);
        $data->modified = $data->created;

        $data->save();

        return $data;

    }

}
