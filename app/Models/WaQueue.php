<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class WaQueue extends Model
{
    use HasFactory;


    protected $attributes = [
        "status" => "queue",
        "number" => "",
        "message" => "",
        "website" => [
            "_id" => "0",
            "name" => "System"
        ],
        "database" => [
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
        "status",
        "number",
        "message",
        "website->_id",
        "website->name",
        "database->_id",
        "database->name",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "waQueue";

    public $timestamps = false;
}
