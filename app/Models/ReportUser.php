<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class ReportUser extends Model {


    use HasFactory;


    protected $attributes = [
        "date" => "",
        "status" => [
            "names" => [],
            "totals" => []
        ],
        "total" => 0,
        "user" => [
            "_id" => "0",
            "avatar" => "",
            "name" => "System",
            "username" => "system"
        ],
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
        "date",
        "status->names",
        "status->totals",
        "total",
        "user->_id",
        "user->avatar",
        "user->name",
        "user->username",
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

    protected $table = "reportUser";

    public $timestamps = false;


}
