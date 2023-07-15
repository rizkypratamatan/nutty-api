<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Models\MessageTemplate;
use Illuminate\Support\Facades\DB;

class MessageTemplateModel
{

    public function getAll($auth, $limit=10, $offset=0, $filter = [])

    {
        // $user = AuthenticationComponent::toUser($this->request);

        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $data = DB::table("message_templates_" . $auth->_id)->take($limit)->skip($offset);
        $countData = DB::table("message_templates_" . $auth->_id);

        if(!empty($filter['name'])){
            $data = $data->where('name', 'LIKE', $filter['name']."%");
            $countData = $countData->where('name', 'LIKE', $filter['name']."%");
        }

        $data = $data->orderBy('_id', 'DESC')->get();
        $counData = $countData->count();

        $response = [
            "data" => $data,
            "total_data" => $counData
        ];

        return $response;
    }



    public static function add($data, $account)
    {

        $arr = [
            "name" => $data->name,
            "format" => $data->format,
            "created" => DataComponent::initializeTimestamp($account),
            "modified" => DataComponent::initializeTimestamp($account)
        ];

        return DB::table("message_templates_".$account->_id)->insert($arr);
    }

    public static function delete($id, $account)
    {
        return DB::table("message_templates_".$account->_id)->where("_id", $id)->delete();
    }

    public static function getById($id, $account)
    {
        // $account = AuthenticationComponent::toUser($this->request);
        return DB::table("message_templates_".$account->_id)
                    ->where("_id", $id)
                    ->first();
    }

    public static function updateById($data, $account)
    {
        $arr = [
            "name" => $data->name,
            "format" => $data->format,
            "modified" => DataComponent::initializeTimestamp($account)
        ];

        return DB::table("message_templates_".$account->_id)
                ->where("_id", $data->id)
                ->update($arr);
    }

    public static function getRandomMessage($account){
        $data = new MessageTemplate();
        $data->setTable('message_templates_'.$account->_id);

        $total_data = $data->count();
        $randomNumber = rand(0, ($total_data-1));

        $data = $data->take(1)->skip($randomNumber)->first();

        return $data;
    }

}
