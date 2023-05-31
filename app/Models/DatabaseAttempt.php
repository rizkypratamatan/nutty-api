<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class DatabaseAttempt extends Model {


    use HasFactory;


    protected $attributes = [
        "contact" => [
            "email" => "",
            "line" => "",
            "michat" => "",
            "phone" => "",
            "telegram" => "",
            "wechat" => "",
            "whatsapp" => ""
        ],
        "status" => [
            "names" => [],
            "totals" => []
        ],
        "total" => 0,
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
        "contact->email",
        "contact->line",
        "contact->michat",
        "contact->phone",
        "contact->telegram",
        "contact->wechat",
        "contact->whatsapp",
        "status->names",
        "status->totals",
        "total",
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

    protected $table = "databaseAttempt";

    public $timestamps = false;


}
