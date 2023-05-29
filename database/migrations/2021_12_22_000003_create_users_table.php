<?php

use App\Components\DataComponent;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;


class CreateUsersTable extends Migration {


    private function createIndex($table) {

        $table->string("avatar")->index();
        $table->string("city")->index();
        $table->string("contact.email")->index();
        $table->string("contact.fax")->index();
        $table->string("contact.line")->index();
        $table->string("contact.michat")->index();
        $table->string("contact.phone")->index();
        $table->string("contact.wechat")->index();
        $table->string("contact.whatsapp")->index();
        $table->string("country")->index();
        $table->string("gender")->index();
        $table->string("language")->index();
        $table->string("name")->index();
        $table->string("nucode")->index();
        $table->string("password.main")->index();
        $table->string("password.recovery")->index();
        $table->string("role._id")->index();
        $table->string("role.name")->index();
        $table->string("state")->index();
        $table->string("status")->index();
        $table->string("street")->index();
        $table->string("type")->index();
        $table->string("username")->index();
        $table->string("zip")->index();
        $table->date("created.timestamp")->index();
        $table->date("modified.timestamp")->index();

    }


    public function up() {

        if(!Schema::hasTable("user")) {

            Schema::create("user", function(Blueprint $table) {

                $this->createIndex($table);

            });

            $user = new User();
            $user->avatar = "";
            $user->city = "";
            $user->contact = [
                "email" => "",
                "fax" => "",
                "line" => "",
                "michat" => "",
                "phone" => "",
                "telegram" => "",
                "wechat" => "",
                "whatsapp" => ""
            ];
            $user->country = "";
            $user->gender = "";
            $user->group = [
                "_id" => "0",
                "name" => "System"
            ];
            $user->language = "";
            $user->name = "System";
            $user->nucode = "system";
            $user->password = [
                "main" => Crypt::encryptString("System123_!!"),
                "recovery" => Crypt::encryptString("System123_!!")
            ];
            $user->privilege = [
                "database" => "7777",
                "report" => "7777",
                "user" => "7777",
                "userGroup" => "7777",
                "userRole" => "7777",
                "website" => "7777",
                "worksheet" => "7777",
                "setting" => "7777",
                "settingApi" => "7777",
                "whatsapp" => "7777",
                "sms" => "7777",
            ];
            $user->role = [
                "_id" => "0",
                "name" => "System"
            ];
            $user->state = "";
            $user->status = "Active";
            $user->street = "";
            $user->type = "Administrator";
            $user->username = "system";
            $user->zip = "";
            $user->created = DataComponent::initializeTimestamp(DataComponent::initializeSystemAccount());
            $user->modified = DataComponent::initializeTimestamp(DataComponent::initializeSystemAccount());
            $user->save();

        } else {

            Schema::table("user", function(Blueprint $table) { 
                $this->createIndex($table);
            });

        }

    }


    public function down() {

        Schema::dropIfExists("user");

    }


}
