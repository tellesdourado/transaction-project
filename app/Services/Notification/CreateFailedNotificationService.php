<?php

namespace App\Services\Notification;

use App\Repositories\Notification\Interfaces\IFailedNotificationRepository;
use App\Services\IBaseService;

class CreateFailedNotificationService implements IBaseService
{
    private $failedNotificationRepository;

    public function __construct(IFailedNotificationRepository $failedNotificationRepository)
    {
        $this->failedNotificationRepository = $failedNotificationRepository;
    }

    public function execute(array $attributes): object
    {
        $filed_notification = $this->failedNotificationRepository->save($attributes);

        return $filed_notification;
    }
}
