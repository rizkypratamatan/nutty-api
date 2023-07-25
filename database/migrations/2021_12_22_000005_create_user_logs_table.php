<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateUserLogsTable extends Migration {


    private function createIndex($table) {

        $table->string("authentication")->index();
        $table->string("agent.ip")->index();
        $table->string("description")->index();
        $table->string("type")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }


    public function up() {

        if(!Schema::hasTable("userLog")) {

            Schema::create("userLog", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("userLog", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists('userLog');

    }


}
