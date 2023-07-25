<?php

namespace App\Jobs;

use App\Repository\EmailLogModel;
use App\Services\Gateway\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $account;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $account)
    {
        
        $this->request = $request;
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipients = explode(",", $this->request['emails']);
        $service = new EmailService();

        foreach($recipients as $recipient){
            $data = $service->initializeData($this->request, $recipient);
            $service->sendEmail($recipient, $data);

            $model = new EmailLogModel();
            $model->insertDB($data, $this->account);
        }
    }
}
