<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateLicensesTable extends Migration {


    private function createIndex($table) {

        $table->string("nucode")->unique();
        $table->integer("seat")->index();
        $table->string("status")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }


    public function up() {

        if(!Schema::hasTable("license")) {

            Schema::create("license", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("license", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists("license");

    }


}
