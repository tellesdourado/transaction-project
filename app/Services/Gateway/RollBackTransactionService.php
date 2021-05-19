<?php

namespace App\Services\Gateway;

use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Repositories\Gateway\Interfaces\ITransactionRepository;
use App\Services\IBaseService;
use App\Services\Users\FindUserService;

class RollBackTransactionService implements IBaseService
{
    private $transactionRepository;
    private $findUserService;
    public function __construct(
        ITransactionRepository $transactionRepository,
        FindUserService $findUserService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->findUserService = $findUserService;
    }

    public function execute(array $attributes): ?object
    {
        $transaction = $this->transactionRepository->findOne($attributes['id']);

        $sender   = $this->findUserService->execute(array("user_id"=>$transaction->sender_id));
        $receiver = $this->findUserService->execute(array("user_id"=>$transaction->receiver_id));


        $receiver->wallet->balance = floatval($receiver->wallet->balance) - floatval($transaction->value);

        $sender->wallet->balance = floatval($sender->wallet->balance) + floatval($transaction->value);

        if (!$receiver->wallet->save()) {
            throw new DefaultApplicationException('Failed to update wallet.', 401);
        }

        if (!$sender->wallet->save()) {
            $receiver->wallet->balance = floatval($receiver->wallet->balance) + floatval($transaction->value);
            $receiver->wallet->save();
            throw new DefaultApplicationException('Failed to update wallet.', 401);
        }

        $this->transactionRepository->delete($attributes['id']);

        return $transaction;
    }
}
