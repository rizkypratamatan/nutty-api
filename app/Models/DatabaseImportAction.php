<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class DatabaseImportAction extends Model {


    use HasFactory;


    protected $attributes = [
        "accounts" => [],
        "databaseImport" => [
            "_id" => "0",
            "file" => "System"
        ],
        "crms" => [],
        "inserts" => [],
        "groups" => [],
        "phones" => [],
        "telemarketers" => [],
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
        "accounts",
        "databaseImport->_id",
        "databaseImport->file",
        "crms",
        "inserts",
        "groups",
        "phones",
        "telemarketers",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "databaseImportAction";

    public $timestamps = false;


}
