<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class Database extends Model {


    use HasFactory;


    protected $attributes = [
        "city" => "",
        "contact" => [
            "email" => "",
            "line" => "",
            "michat" => "",
            "phone" => "",
            "telegram" => "",
            "wechat" => "",
            "whatsapp" => ""
        ],
        "country" => "",
        "crm" => [
            "_id" => "0",
            "avatar" => "",
            "name" => "System",
            "username" => "system"
        ],
        "gender" => "",
        "group" => [
            "_id" => "0",
            "name" => "System"
        ],
        "import" => [
            "_id" => "0",
            "file" => ""
        ],
        "language" => "",
        "name" => "",
        "reference" => "",
        "state" => "",
        "status" => "",
        "street" => "",
        "telemarketer" => [
            "_id" => "0",
            "avatar" => "",
            "name" => "System",
            "username" => "system"
        ],
        "zip" => "",
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
        "city",
        "contact->email",
        "contact->line",
        "contact->michat",
        "contact->phone",
        "contact->telegram",
        "contact->wechat",
        "contact->whatsapp",
        "country",
        "crm->_id",
        "crm->avatar",
        "crm->name",
        "crm->username",
        "gender",
        "group->_id",
        "group->name",
        "import->_id",
        "import->file",
        "language",
        "name",
        "reference",
        "state",
        "status",
        "street",
        "telemarketer->_id",
        "telemarketer->avatar",
        "telemarketer->name",
        "telemarketer->username",
        "zip",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "database";

    public $timestamps = false;


}
