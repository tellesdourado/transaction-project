<?php

namespace App\Repositories\Users\Interfaces;

use App\Repositories\IBaseRepository;

interface IUserRepository extends IBaseRepository
{
    public function findUserAuthorization($user_id): ?object;

    public function findUserWallet($user_id): ?object;
}
