<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $attributes = [
        "name" => "",
        "value" => "",
        "created" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "username" => "",
            ]
        ],
        "modified" => [
            "timestamp" => "",
            "user" => [
                "_id" => "",
                "username" => "",
            ]
        ],
    ];

    protected $fillable = [
        "name",
        "value",
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "settings";

    public $timestamps = false;

    // protected $casts = ['created.timestamp' => 'datetime:d.m.Y'];

    // public function createdDate()
    // {
    //     return $this->embedsOne(Address::class);
    // }
}
