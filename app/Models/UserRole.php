<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $attributes = [
        "description" => "",
        "name" => "",
        "nucode" => "",
        "privilege" => [
            "database" => "0000",
            "report" => "0000",
            "setting" => "0000",
            "settingApi" => "0000",
            "user" => "0000",
            "userGroup" => "0000",
            "userRole" => "0000",
            "website" => "0000",
            "worksheet" => "0000",
        ],
        "status" => "",
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
        "description",
        "name",
        "nucode",
        "privilege->database",
        "privilege->report",
        "privilege->setting",
        "privilege->settingApi",
        "privilege->user",
        "privilege->userGroup",
        "privilege->userRole",
        "privilege->website",
        "privilege->worksheet",
        "status",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "userRole";

    public $timestamps = false;
}
