<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Hash;
use App\Repositories\Users\Interfaces\IUserRepository;
use App\Services\IBaseService;

class CreateUserService implements IBaseService
{
    private $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(array $attributes): object
    {
        $attributes['password'] = Hash::make($attributes['password']);
        $user = $this->userRepository->save($attributes);
        return $user;
    }
}
