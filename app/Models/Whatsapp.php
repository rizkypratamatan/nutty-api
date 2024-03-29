<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Whatsapp extends Model
{
    use HasFactory;
    protected $attributes = [
        "account" => 0,
        "campaign" => "",
        "recipient" => "",
        "type" => "",
        "message" => "",
        "media_file" => "",
        "media_url" => "",
        "media_type" => "",
        "document_file" => "",
        "document_url" => "",
        "document_type" => "",
        "button_1" => "",
        "button_2" => "",
        "button_3" => "",
        "list_title" => "",
        "menu_title" => "",
        "footer" => "",
        "format" => "",
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
        "account",
        "campaign",
        "recipient",
        "type",
        "message",
        "media_file",
        "media_url",
        "media_type",
        "document_file",
        "document_url",
        "document_type",
        "button_1",
        "button_2",
        "button_3",
        "list_title",
        "menu_title",
        "footer",
        "format",
        "shortener",
        "created->timestamp",
        "created->user->_id",
        "modified->timestamp",
        "modified->user->_id",
    ];

    protected $table = "whatsappLogs";

    public $timestamps = false;

}
