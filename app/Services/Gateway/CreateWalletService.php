<?php

namespace App\Services\Gateway;

use App\Services\IBaseService;
use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Helpers\ValidateFields;
use App\Repositories\Gateway\Interfaces\IWalletRepository;
use App\Repositories\Users\Interfaces\IUserRepository;

class CreateWalletService implements IBaseService
{
    private $walletRepository;
    private $userRepository;

    public function __construct(IWalletRepository $walletRepository, IUserRepository $userRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->userRepository   = $userRepository;
    }

    public function execute(array $attributes): object
    {
        $fieldsRequired = ['user_id'];

        ValidateFields::required($fieldsRequired, $attributes);

        $userExist = $this->userRepository->findOne($attributes['user_id']);

        if (!$userExist) {
            throw new DefaultApplicationException('User does not exist.', 401);
        }

        $wallet['user_id'] = $attributes['user_id'];
        $wallet['balance'] = 300.00;

        $wallet = $this->walletRepository->save($wallet);

        return $wallet;
    }
}
