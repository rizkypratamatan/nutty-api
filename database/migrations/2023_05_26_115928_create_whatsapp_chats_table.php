<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function createIndex($table) {
        /*
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
        */
        $table->integer("account")->index();
        $table->string("campaign")->index();
        $table->string("recipient")->index();
        $table->string("type")->index();
        $table->string("message")->index();
        $table->string("media_file")->index();
        $table->string("media_url")->index();
        $table->string("media_type")->index();
        $table->string("document_file")->index();
        $table->string("document_url")->index();
        $table->string("document_type")->index();
        $table->string("button_1")->index();
        $table->string("button_2")->index();
        $table->string("button_3")->index();
        $table->string("list_title")->index();
        $table->string("menu_title")->index();
        $table->string("footer")->index();
        $table->string("format")->index();
        $table->string("shortener")->index();
        $table->date("created.timestamp")->index();
        $table->date("created.user._id")->index();
        $table->date("created.user.username")->index();
        $table->date("modified.timestamp")->index();
        $table->date("modified.user.username")->index();

    }


    public function up() {

        if(!Schema::hasTable("whatsapp_chats")) {

            Schema::create("whatsapp_chats", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("whatsapp_chats", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists("whatsapp_chats");

    }
};
