<?php

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Repositories\Users\Interfaces\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findUserAuthorization($user_id): ?object
    {
        return $this->obj->with('type')->with('wallet')->where('id', '=', $user_id)->first();
    }

    public function findUserWallet($user_id): ?object
    {
        return $this->obj->with('wallet')->where('id', '=', $user_id)->first();
    }
}
