<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private function createIndex($table)
    {
        $table->string("interval_sms")->index();
        $table->string("interval_wa")->index();
        $table->string("interval_email")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();
    }

    public function up()
    {
        if (!Schema::hasTable("settings")) {

            Schema::create("settings", function (Blueprint $table) {

                $this->createIndex($table);
            });
        } else {

            Schema::table("settings", function (Blueprint $table) {

                $this->createIndex($table);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
