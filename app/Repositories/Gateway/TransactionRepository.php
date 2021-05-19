<?php

namespace App\Repositories\Gateway;

use App\Repositories\BaseRepository;
use App\Models\Transaction;
use App\Repositories\Gateway\Interfaces\ITransactionRepository;

class TransactionRepository extends BaseRepository implements ITransactionRepository
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        parent::__construct($transaction);
    }
}
