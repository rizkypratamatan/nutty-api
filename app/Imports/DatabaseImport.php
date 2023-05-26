<?php

namespace App\Imports;

use App\Models\Database;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class DatabaseImport implements ToModel {


    public function model(array $row) {

        return new Database([
            "city" => "",
            "contact->email" => $row[3],
            "contact->line" => $row[6],
            "contact->michat" => $row[8],
            "contact->phone" => $row[4],
            "contact->telegram" => "",
            "contact->wechat" => $row[7],
            "contact->whatsapp" => $row[5],
            "country" => "",
            "crm->_id" => "",
            "crm->avatar" => "",
            "crm->name" => "",
            "crm->username" => $row[17],
            "gender" => "",
            "group->_id" => "",
            "group->name" => "",
            "import->_id" => "",
            "import->file" => "",
            "language" => "",
            "name" => $row[1],
            "reference" => "",
            "state" => "",
            "status" => "",
            "street" => "",
            "telemarketer->_id" => "",
            "telemarketer->avatar" => "",
            "telemarketer->name" => "",
            "telemarketer->username" => $row[16],
            "zip" => "",
            "created->timestamp" => "",
            "created->user->_id" => "",
            "created->user->username" => "",
            "modified->timestamp" => "",
            "modified->user" => "",
            "modified->user->_id" => "",
            "modified->user->username" => ""
        ]);

    }


}
