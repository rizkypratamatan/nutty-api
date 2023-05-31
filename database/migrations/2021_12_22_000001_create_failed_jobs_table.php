<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateFailedJobsTable extends Migration {


    public function up() {

        Schema::create("failed_jobs", function(Blueprint $table) {

        });

    }


    public function down() {

        Schema::dropIfExists("failed_jobs");

    }


}
