<?php

namespace App\Services\Gateway;

use App\Services\IBaseService;
use App\Helpers\ValidateFields;
use App\Repositories\Gateway\Interfaces\ITransactionRepository;

class CreateTransactionService implements IBaseService
{
    private $transactionRepository;

    public function __construct(ITransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(array $attributes): object
    {
        ValidateFields::required(['value', 'receiver_id', 'sender_id'], $attributes);
        $transaction = $this->transactionRepository->save($attributes);
        return $transaction;
    }
}
