<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class DatabaseLog extends Model {


    use HasFactory;


    protected $attributes = [
        "database" => [
            "_id" => "0",
            "name" => "System"
        ],
        "reference" => "",
        "status" => "",
        "user" => [
            "_id" => "0",
            "avatar" => "",
            "name" => "System",
            "username" => "system"
        ],
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
        "database->_id",
        "database->name",
        "reference",
        "status",
        "user->_id",
        "user->avatar",
        "user->name",
        "user->username",
        "website->_id",
        "website->name",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "userCallLog";

    public $timestamps = false;


}
