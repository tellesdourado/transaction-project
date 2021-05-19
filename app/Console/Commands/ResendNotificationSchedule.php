<?php

namespace App\Console\Commands;

use App\Services\Notification\FindFailedNotificationService;
use App\Services\Notification\SendNotificationService;
use Illuminate\Console\Command;

class ResendNotificationSchedule extends Command
{
    protected $signature = 'resend:notification';
    protected $description = 'search on failed_notifications table for new messages to resend';
    private $findFailedNotificationService;
    private $sendNotificationService;
    public function __construct(
        FindFailedNotificationService $findFailedNotificationService,
        SendNotificationService $sendNotificationService
    ) {
        parent::__construct();
        $this->findFailedNotificationService = $findFailedNotificationService;
        $this->sendNotificationService = $sendNotificationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $failed_notifications = $this->findFailedNotificationService->execute();

        foreach ($failed_notifications as $key=> $notification) {
            $this->sendNotificationService->execute(array("transaction_id" => $notification->transaction_id));
            $notification->delete();
        }
    }
}
