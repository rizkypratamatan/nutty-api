<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateUserRolesTable extends Migration {


    private function createIndex($table) {

        $table->integer("description")->index();
        $table->string("name")->index();
        $table->string("nucode")->index();
        $table->string("status")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }


    public function up() {

        if(!Schema::hasTable("userRole")) {

            Schema::create("userRole", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("userRole", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists("userRole");

    }


}
