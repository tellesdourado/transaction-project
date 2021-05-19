<?php

namespace App\Services\Users;

use App\Repositories\Users\Interfaces\IUserRepository;
use App\Services\IBaseService;

class ShowUsersService implements IBaseService
{
    private $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(array $attributes=[]): object
    {
        return $this->userRepository->all();
    }
}
