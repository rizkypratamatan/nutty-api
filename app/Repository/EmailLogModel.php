<?php

namespace App\Repository;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Jobs\ProcessBulkEmail;
use App\Models\EmailMessage;
use App\Services\Gateway\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmailLogModel
{
    protected $service;

    public function __construct()
    {
        $this->service = new EmailService();
    }

    public function getAll($limit = 10, $offset = 0, $auth, $filter=[])
    {
        $response = [
            "data" => null,
            "total_data" => 0
        ];

        $email = DB::table("emailLogs_" . $auth->_id)->take($limit)->skip($offset);
        $countData = DB::table("emailLogs_" . $auth->_id);

        if (!empty($filter['from_name'])) {
            $email = $email->where('from_name', 'LIKE', "%" . $filter['from_name'] . "%");
            $countData = $countData->where('from_name', 'LIKE', "%" . $filter['from_name'] . "%");
        }

        if (!empty($filter['email'])) {
            $email = $email->where('email', 'LIKE', "%" . $filter['email'] . "%");
            $countData = $countData->where('email', 'LIKE', "%" . $filter['email'] . "%");
        }

        if (!empty($filter['subject'])) {
            $email = $email->where('subject', 'LIKE', "%" . $filter['subject'] . "%");
            $countData = $countData->where('subject', 'LIKE', "%" . $filter['subject'] . "%");
        }

        if (!empty($filter['message'])) {
            $email = $email->where('message', 'LIKE', "%" . $filter['message'] . "%");
            $countData = $countData->where('message', 'LIKE', "%" . $filter['message'] . "%");
        }

        if (!empty($filter['status'])) {
            $email = $email->where('status', 'LIKE', "%" . $filter['status'] . "%");
            $countData = $countData->where('status', 'LIKE', "%" . $filter['status'] . "%");
        }

        $data = $email->orderBy('_id', 'DESC')->get();
        $counData = $countData->count();

        $response = [
            "data" => $data,
            "total_data" => $counData
        ];
        
        return $response;
    }

    public function delete($id, $auth)
    {
        return DB::table("emailLogs_" . $auth->_id)
            ->where('_id', $id)
            ->delete();
    }

    public function getById($id, $auth)
    {

        return DB::table("emailLogs_" . $auth->_id)
            ->where('_id', $id)
            ->first();
    }

    public function sendSingle($request)
    {

        $data = $this->service->initializeData($request->all(), $request->email);
        $this->service->sendEmail($request->email, $data);
        $this->insertDB($data, AuthenticationComponent::toUser($request));

        return [
            'result' => true,
            'response' => "Email has been queued",
            'data' => false
        ];
    }

    public function sendBulk($request)
    {
        ProcessBulkEmail::dispatch($request->all(), AuthenticationComponent::toUser($request));

        return [
            'result' => true,
            'response' => "Email has been queued",
            'data' => false
        ];
    }

    public function insertDB($data, $account)
    {

        unset($data['secret']);
        unset($data['campaign']);
        unset($data['numbers']);

        $data['status'] = "sent";
        $data['created'] = DataComponent::initializeTimestamp($account);
        $data['modified'] = DataComponent::initializeTimestamp($account);

        return DB::table("emailLogs_" . $account->_id)->insert($data);
    }
}
