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

    protected $user;
    protected $request;

    public function __construct(Request $request)
    {   
        $this->user = AuthenticationComponent::toUser($request);
        $this->request = $request;
    }

    public function deleteLicense($id)
    {

        return License::where('_id', $id)->delete();
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

    public function addLicense($data)
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
            "created" => DataComponent::initializeTimestamp($this->user),
            "modified" => DataComponent::initializeTimestamp($this->user)
        ];

        return DB::table('license')
            ->insert($arr);
    }

    public function getLicense($limit=10, $offset=0)
    {   
        return License::get()->take($limit)->skip($offset);
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

}
