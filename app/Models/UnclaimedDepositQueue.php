<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class UnclaimedDepositQueue extends Model {


    use HasFactory;


    protected $attributes = [
        "date" => "",
        "nucode" => "",
        "status" => "",
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
        "nucode",
        "status",
        "website->_id",
        "website->name",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "unclaimedDepositQueue";

    public $timestamps = false;


}
