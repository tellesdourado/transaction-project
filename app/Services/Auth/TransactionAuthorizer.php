<?php

namespace App\Services\Auth;

use GuzzleHttp\Client;
use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Services\IBaseService;

class TransactionAuthorizer implements IBaseService
{
    public function __construct()
    {
    }

    public function execute(array $attributes = []): bool
    {
        $client = new Client();
        try {
            $client->get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
        } catch (\Throwable $th) {
            throw new DefaultApplicationException('Transaction external authorization failed', 401);
        }

        return true;
    }
}
