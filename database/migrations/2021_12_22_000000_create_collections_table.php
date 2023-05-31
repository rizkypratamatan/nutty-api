<?php

use App\Components\DataComponent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;


class CreateCollectionsTable extends Migration {


    public function up() {

        if(config("app.nucode") != "PUBLIC") {

            DataComponent::initializeCollectionByNucode(config("app.nucode"));

        }

    }


    public function down() {

        if(config("app.nucode") != "PUBLIC") {

            Schema::dropIfExists("databaseAttempt_" . config("app.nucode"));
            Schema::dropIfExists("databaseImport_" . config("app.nucode"));
            Schema::dropIfExists("databaseImportAction_" . config("app.nucode"));
            Schema::dropIfExists("playerAttempt_" . config("app.nucode"));
            Schema::dropIfExists("report_" . config("app.nucode"));

        }

    }


}
