<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class License extends Model {


    use HasFactory;


    protected $attributes = [
        "nucode" => "",
        "package" => [
            "expired" => "",
            "payment" => [
                "last" => "",
                "next" => ""
            ],
            "start" => "",
            "status" => "",
            "trial" => ""
        ],
        "user" => [
            "primary" => [
                "_id" => "0",
                "avatar" => "",
                "name" => "",
                "username" => ""
            ],
            "total" => 0
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
        "nucode",
        "package->expired",
        "package->payment->last",
        "package->payment->next",
        "package->start",
        "package->status",
        "package->trial",
        "user->primary->_id",
        "user->primary->avatar",
        "user->primary->name",
        "user->primary->username",
        "user->total",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "license";

    public $timestamps = false;


}
