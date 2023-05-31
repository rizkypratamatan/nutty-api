<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateWebsitesTable extends Migration {


    private function createIndex($table) {

        $table->string("api.nexus.code")->index();
        $table->string("api.nexus.salt")->index();
        $table->string("api.nexus.url")->index();
        $table->integer("description")->index();
        $table->string("name")->index();
        $table->string("nucode")->index();
        $table->string("status")->index();
        $table->string("sync")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }


    public function up() {

        if(!Schema::hasTable("website")) {

            Schema::create("website", function(Blueprint $table) {

                $this->createIndex($table);

            });

        } else {

            Schema::table("website", function(Blueprint $table) {

                $this->createIndex($table);

            });

        }

    }


    public function down() {

        Schema::dropIfExists("website");

    }


}
