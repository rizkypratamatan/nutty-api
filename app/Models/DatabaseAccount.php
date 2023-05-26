<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class DatabaseAccount extends Model {


    use HasFactory;


    protected $attributes = [
        "database" => [
            "_id" => "0",
            "name" => ""
        ],
        "deposit" => [
            "average" => [
                "amount" => "0",
            ],
            "first" => [
                "amount" => "0",
                "timestamp" => ""
            ],
            "last" => [
                "amount" => "0",
                "timestamp" => ""
            ],
            "total" => [
                "amount" => "0"
            ]
        ],
        "games" => [],
        "login" => [
            "average" => [
                "daily" => 0,
                "monthly" => 0,
                "weekly" => 0,
                "yearly" => 0
            ],
            "first" => [
                "timestamp" => ""
            ],
            "last" => [
                "timestamp" => ""
            ],
            "total" => [
                "amount" => "0"
            ]
        ],
        "reference" => "",
        "register" => [
            "timestamp" => ""
        ],
        "username" => "",
        "withdrawal" => [
            "average" => [
                "amount" => "0"
            ],
            "first" => [
                "amount" => "0",
                "timestamp" => ""
            ],
            "last" => [
                "amount" => "0",
                "timestamp" => ""
            ],
            "total" => [
                "amount" => "0"
            ]
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
        "deposit->average->amount",
        "deposit->average->timestamp",
        "deposit->first->amount",
        "deposit->first->timestamp",
        "deposit->last->amount",
        "deposit->last->timestamp",
        "deposit->total->amount",
        "games",
        "login->average->daily",
        "login->average->monthly",
        "login->average->weekly",
        "login->average->yearly",
        "login->first->timestamp",
        "login->last->timestamp",
        "login->total->amount",
        "reference",
        "register->timestamp",
        "username",
        "withdrawal->average->amount",
        "withdrawal->first->amount",
        "withdrawal->first->timestamp",
        "withdrawal->last->amount",
        "withdrawal->last->timestamp",
        "withdrawal->total->amount",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "databaseAccount";

    public $timestamps = false;


}
