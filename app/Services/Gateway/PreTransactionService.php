<?php

namespace App\Services\Gateway;

use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Services\IBaseService;
use App\Services\Auth\TransactionAuthorizer;
use App\Services\Notification\SendNotificationService;
use App\Services\Users\FindUserService;

class PreTransactionService implements IBaseService
{
    private $createTransactionService;
    private $transactionAuthorizer;
    private $findUserService;
    private $sendNotificationService;

    public function __construct(
        CreateTransactionService $createTransactionService,
        TransactionAuthorizer $transactionAuthorizer,
        FindUserService $findUserService,
        SendNotificationService $sendNotificationService
    ) {
        $this->createTransactionService  = $createTransactionService;
        $this->transactionAuthorizer     = $transactionAuthorizer;
        $this->findUserService            = $findUserService;
        $this->sendNotificationService    = $sendNotificationService;
    }

    public function execute(array $attributes): object
    {
        $sender_user = $this->findUserService->execute(array("user_id"=> $attributes['payer']));

        if (!$sender_user->type->send_authorization) {
            throw new DefaultApplicationException('This user cannot do a transaction', 401);
        }

        $receiver_user = $this->findUserService->execute(array("user_id"=> $attributes['payee']));

        if (!$receiver_user->type->receive_authorization) {
            throw new DefaultApplicationException('This user cannot do a transaction.', 401);
        }

        $this->transactionAuthorizer->execute();

        if (floatval($sender_user->wallet->balance) < floatval($attributes['value'])) {
            throw new DefaultApplicationException('Insufficient funds to proceed.', 401);
        }

        $receiver_user->wallet->balance = floatval($receiver_user->wallet->balance) + floatval($attributes['value']);

        $sender_user->wallet->balance = floatval($sender_user->wallet->balance) - floatval($attributes['value']);

        if (!$receiver_user->wallet->save()) {
            throw new DefaultApplicationException('Failed to update wallet.', 401);
        }

        if (!$sender_user->wallet->save()) {
            $receiver_user->wallet->balance = floatval($receiver_user->wallet->balance) - floatval($attributes['value']);
            $receiver_user->wallet->save();
            throw new DefaultApplicationException('Failed to update wallet.', 401);
        }

        $transactionDTO = array(
            "sender_id"   =>  $sender_user->id,
            "receiver_id" =>  $receiver_user->id,
            "value"       =>  $attributes['value'],
        );

        $transaction = $this->createTransactionService->execute($transactionDTO);

        $this->sendNotificationService->execute(array("transaction_id"=>$transaction->id));

        return $transaction;
    }
}
