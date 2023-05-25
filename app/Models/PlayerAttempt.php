<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class PlayerAttempt extends Model {


    use HasFactory;


    protected $attributes = [
        "status" => [
            "names" => [],
            "totals" => []
        ],
        "total" => 0,
        "username" => "",
        "website" => [
            "ids" => [],
            "names" => [],
            "totals" => []
        ],
        "created" => [
            "timestamp" => "",
            "user" => [
                "_id" => "0",
                "username" => "System"
            ]
        ],
        "modified" => [
            "timestamp" => "",
            "user" => [
                "_id" => "0",
                "username" => "System"
            ]
        ]
    ];

    protected $fillable = [
        "status->names",
        "status->totals",
        "total",
        "username",
        "website->ids",
        "website->names",
        "website->totals",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "playerAttempt";

    public $timestamps = false;


}
