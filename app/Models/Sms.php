<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;
    protected $attributes = [
        "mode" => 'devices',
        "phone" => "",
        "message" => "",
        "device" => "",
        "sim" => 1,
        "priority" => 1,
        "shortener" => "",
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
        "mode",
        "phone",
        "message",
        "device",
        "sim",
        "priority",
        "shortener",
        "created->timestamp",
        "created->user->_id",
        "modified->timestamp",
        "modified->user->_id",
    ];

    protected $table = "sms_messages";

    public $timestamps = false;

    protected $casts = ['created.timestamp' => 'datetime:d.m.Y'];

}
