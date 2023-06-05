<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Jobs\DatabaseAccountJob;
use App\Jobs\DatabaseAccountSyncJob;
use App\Jobs\PlayerTransactionJob;
use App\Jobs\PlayerTransactionSyncJob;
use App\Models\DatabaseAccount;
use App\Repository\DatabaseAccountModel;
use App\Repository\WebsiteModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use stdClass;


class SystemService {


    public static function findPlayerTransaction($date) 
    {

        $result = new DatabaseAccount();
        $result->response = "Failed to find player transaction";
        $result->result = false;

        $websiteByStatusNotApiNexusSaltStart = WebsiteModel::findByStatusNotApiNexusSaltStart("", "", "Synced");
        $loop = ceil(count($websiteByStatusNotApiNexusSaltStart) / config("app.api.nexus.batch.size.website"));

        $delay = Carbon::now();

        $fromDate = Carbon::createFromFormat("Y-m-d", $date)->subDays(1)->format("Y-m-d");
        $toDate = Carbon::createFromFormat("Y-m-d", $date)->format("Y-m-d");

        // for($i = 0; $i < $loop; $i++) 
        // {

        //     dispatch((new PlayerTransactionJob($fromDate, $i + 1, $toDate)))->delay($delay->addMinutes(config("app.api.nexus.batch.delay")));

        // }

        if(!$websiteByStatusNotApiNexusSaltStart->isEmpty()) {

            foreach($websiteByStatusNotApiNexusSaltStart as $value) {

                $databaseAccounts = DatabaseAccountModel::findAll($value->_id);
                $loop = ceil(count($databaseAccounts) / config("app.api.nexus.batch.size.player"));

                // for($i = 0; $i < $loop; $i++) 
                // {

                //     dispatch((new DatabaseAccountJob($loop, $i + 1, $value->_id)))->delay($delay->addMinutes(config("app.api.nexus.batch.delay")));

                // }

            }

        }

        $result->response = "Player transaction found";
        $result->result = true;

        return $result;

    }


    // public static function syncPlayerTransaction($websiteId) 
    // {

    //     $result = new stdClass();
    //     $result->response = "Failed to sync player transaction";
    //     $result->result = false;

    //     $websiteById = WebsiteRepository::findOneById($websiteId);

    //     if(!empty($websiteById)) {

    //         if(!Schema::hasTable("nexusPlayerTransaction_" . $websiteId)) {

    //             Schema::create("nexusPlayerTransaction_" . $websiteId, function(Blueprint $table) {

    //                 DataComponent::createNexusPlayerTransactionIndex($table);

    //             });

    //         }

    //         $delay = Carbon::now();

    //         if(!empty($websiteById->api["nexus"]["code"]) && !empty($websiteById->api["nexus"]["salt"]) && !empty($websiteById->api["nexus"]["url"])) {

    //             $date = Carbon::createFromDate($websiteById->start->toDateTime());
    //             $loop = $date->diffInDays(Carbon::now()->addDays(7));
    //             $dateStart = $date->format("Y/m/d");
    //             $dateEnd = $date->addDays(1)->format("Y/m/d");

    //             for($i = 0; $i < $loop; $i++) {

    //                 dispatch((new PlayerTransactionSyncJob($dateEnd, $dateStart, $loop, $websiteById->api["nexus"]["code"], $i + 1, $websiteById->api["nexus"]["salt"], $websiteById->api["nexus"]["url"], $websiteById->_id)))->delay($delay->addMinutes(config("app.api.nexus.batch.delay")));

    //                 $dateStart = $date->format("Y/m/d");
    //                 $dateEnd = $date->addDays(1)->format("Y/m/d");

    //             }

    //         }

    //         $databaseAccountCount = DatabaseAccountRepository::count($websiteById->_id);
    //         $loop = ceil($databaseAccountCount / config("app.api.nexus.batch.size.player"));

    //         for($i = 0; $i < $loop; $i++) {

    //             dispatch((new DatabaseAccountSyncJob($loop, $i + 1, $websiteById->_id)))->delay($delay->addMinutes(config("app.api.nexus.batch.delay")));

    //         }

    //     }

    //     $result->response = "Player transaction synced";
    //     $result->result = true;

    //     return $result;

    // }


}
