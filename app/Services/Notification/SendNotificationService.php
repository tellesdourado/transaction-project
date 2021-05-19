<?php

namespace App\Services\Notification;

use GuzzleHttp\Client;
use App\Services\IBaseService;
use Exception;

class SendNotificationService
{
    private $createFailedNotificationService;

    public function __construct(CreateFailedNotificationService $createFailedNotificationService)
    {
        $this->createFailedNotificationService = $createFailedNotificationService;
    }

    public function execute(array $attributes)
    {
        $client = new Client();
        try {
            $client->request('POST', 'http://o4d9z.mocklab.io/notify', $attributes);
        } catch (\Throwable $th) {
            $this->createFailedNotificationService->execute($attributes);
        }
    }
}
