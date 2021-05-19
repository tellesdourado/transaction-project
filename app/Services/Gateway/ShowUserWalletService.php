<?php

namespace App\Services\Gateway;

use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Services\IBaseService;
use App\Repositories\Users\Interfaces\IUserRepository;

class ShowUserWalletService implements IBaseService
{
    private $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(array $attributes): array
    {
        $user = $this->userRepository->findUserWallet($attributes['id']);

        if (!isset($user->wallet)) {
            throw new DefaultApplicationException('Wallet does not exist.', 401);
        }

        return $user->wallet->toArray();
    }
}
