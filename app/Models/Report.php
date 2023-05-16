<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class Report extends Model {


    use HasFactory;


    protected $attributes = [
        "date" => "",
        "status" => [
            "names" => "",
            "totals" => ""
        ],
        "total" => "",
        "user" => [
            "_id" => "",
            "avatar" => "",
            "name" => "",
            "username" => ""
        ],
        "website" => [
            "ids" => "",
            "names" => "",
            "totals" => ""
        ],
        "created" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "username" => ""
            ]
        ],
        "modified" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "username" => ""
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

    protected $table = "report";

    public $timestamps = false;


}
