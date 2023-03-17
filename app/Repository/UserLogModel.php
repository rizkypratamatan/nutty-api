<?php

namespace App\Repository;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserLogModel
{
    public function insertToLog($data)
    {
        $mytime = Carbon::now();

        $dataLog = [
            'authentication' => Hash::make($data),
            'created' => [
                'timestamp' => $mytime->toDateTimeString()
            ]
        ];
        return DB::table('userLog')->insert($dataLog);
    }
}
