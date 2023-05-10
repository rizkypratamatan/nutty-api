<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    
    protected $attributes = [
        "authentication" => "",
        "agent" => [
            "browser" => [
                "manufacturer" => "",
                "name" => "",
                "renderingEngine" => "",
                "version" => ""
            ],
            "device" => [
                "os" => "",
                "manufacturer" => "",
                "type" => ""
            ],
            "ip" => ""
        ],
        "description" => "",
        "nucode" => "",
        "target" => [
            "_id" => "0",
            "name" => "System"
        ],
        "type" => "",
        "user" => [
            "_id" => "0",
            "username" => "System"
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
        "authentication",
        "agent->browser->manufacturer",
        "agent->browser->name",
        "agent->browser->renderingEngine",
        "agent->browser->version",
        "agent->device->os",
        "agent->device->manufacturer",
        "agent->device->type",
        "agent->ip",
        "description",
        "nucode",
        "target->_id",
        "target->name",
        "type",
        "user->_id",
        "user->username",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "userLog";

    public $timestamps = false;
}
