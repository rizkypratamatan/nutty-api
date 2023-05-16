<?php

namespace App\Repository;

use App\Models\License;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LicenseModel
{

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
            'created' => [
                'timestamp' => $mytime->toDateTimeString(),
                'user' => [
                    // '_id' => '0',
                    // 'username' => 'System'
                ]
            ],
            'modified' => [
                'timestamp' => $mytime->toDateTimeString(),
                'user' => [
                    // '_id' => '0',
                    // 'username' => 'System'
                ]
            ]
        ];

        return DB::table('user')
            ->where('_id', $data->id)->update($arr);
    }

}
