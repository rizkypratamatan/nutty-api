<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class ReportWebsite extends Model {


    use HasFactory;


    protected $attributes = [
        "date" => "",
        "status" => [
            "names" => [],
            "totals" => []
        ],
        "total" => 0,
        "website" => [
            "_id" => "0",
            "name" => "System"
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
        "website->_id",
        "website->name",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "reportWebsite";

    public $timestamps = false;


}
