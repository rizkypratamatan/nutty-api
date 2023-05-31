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

    public function getAll($limit=10, $offset=0)
    {   
        return DB::table("emailLogs_".$this->user->_id)
                            ->take($limit)
                            ->skip($offset)
                            ->get();
        
    }

    public function delete($id)
    {
        return DB::table("emailLogs_".$this->user->_id)
                    ->where('_id', $id)
                    ->delete();
    }

    public function getById($id, $auth=null)
    {
        
        return DB::table("emailLogs_".$this->user->_id)
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

        return DB::table("emailLogs_".$account->_id)->insert($data);
    }

}
