<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function createIndex($table) {
        /*
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
        */
        $table->string("mode")->index();
        $table->string("phone")->index();
        $table->string("message")->index();
        $table->string("device")->index();
        $table->integer("sim")->index();
        $table->integer("priority")->index();
        $table->string("shortener")->index();
        $table->date("created.timestamp")->index();
        $table->date("created.user._id")->index();
        $table->date("created.user.username")->index();
        $table->date("modified.timestamp")->index();
        $table->date("modified.user.username")->index();

    }


    public function up() {

        if(!Schema::hasTable("sms_messages")) {

            Schema::create("sms_messages", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("sms_messages", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists("sms_messages");

    }
};
