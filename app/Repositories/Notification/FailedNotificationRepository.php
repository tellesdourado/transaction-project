<?php

namespace App\Repositories\Notification;

use App\Models\FailedNotifications;
use App\Repositories\BaseRepository;
use App\Repositories\Notification\Interfaces\IFailedNotificationRepository;

class FailedNotificationRepository extends BaseRepository implements IFailedNotificationRepository
{
    protected $failedNotification;

    public function __construct(FailedNotifications $failedNotification)
    {
        parent::__construct($failedNotification);
    }
}
