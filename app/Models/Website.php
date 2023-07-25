<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $attributes = [
        "api" => [
            "nexus" => [
                "code" => "",
                "salt" => "",
                "url" => ""
            ]
        ],
        "description" => "",
        "name" => "",
        "nucode" => "",
        "start" => "",
        "status" => "Inactive",
        "sync" => "NoSync",
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
        "api->nexus->code",
        "api->nexus->salt",
        "api->nexus->url",
        "description",
        "name",
        "nucode",
        "start",
        "status",
        "sync",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "website";

    public $timestamps = false;
}
