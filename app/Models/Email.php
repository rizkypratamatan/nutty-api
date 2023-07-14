<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $attributes = [
        "from_name" => "Nutty CRM",
        "from_email" => "",
        "to_email" => "",
        "subject" => "",
        "cc" => "",
        "bcc" => "",
        "message" => "",
        "attachment" => "",
        "status" => "",
        "initiated_time" => "",
        "schedule_status" => "",
        "created" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => "",
            ]
        ],
        "modified" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "avatar" => "",
                "name" => "",
                "username" => "",
            ]
        ]     
    ];

    protected $fillable = [
        "from_name",
        "from_email",
        "to_email",
        "subject",
        "cc",
        "bcc",
        "message",
        "attachment",
        "status",
        "initiated_time",
        "schedule_status",
        "created->timestamp",
        "created->user->_id",
        "modified->timestamp",
        "modified->user->_id",
    ];

    protected $table = "emailLogs";

    public $timestamps = false;

}