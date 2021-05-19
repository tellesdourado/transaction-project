<?php

namespace App\Services\Users;

use App\Repositories\Users\Interfaces\IUserTypeRepository;
use App\Services\IBaseService;

class ShowUserTypesService implements IBaseService
{
    protected $userTypeRepository;

    public function __construct(IUserTypeRepository $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }

    public function execute(array $attributes = []): object
    {
        $user_types = $this->userTypeRepository->all();
        return $user_types;
    }
}
