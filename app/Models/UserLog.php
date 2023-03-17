<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    protected $table = "user";

    public $timestamps = false;
}
