<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    protected $attributes = [

        "description" => "",
        "name" => "",
        "status" => "",
        "type" => "",
        "website" => [
            "ids" => "",
            "names" => "",            
        ],        
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
        ],
        "nucode" => "",        
    ];

    protected $fillable = [
        "description",
        "name",
        "status",
        "type",
        "website->ids",
        "website->names",       
        "created->timestamp",
        "created->user->_id",
        "created->user->username",
        "modified->timestamp",
        "modified->user->_id",
        "modified->user->username"
    ];

    protected $table = "userGroup";

    public $timestamps = false;

    protected $casts = ['created.timestamp' => 'datetime:d.m.Y'];

    public function createdDate()
    {
        return $this->embedsOne(Address::class);
    }
}
