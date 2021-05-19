<?php

namespace App\Repositories\Users;

use App\Repositories\BaseRepository;
use App\Models\UserTypes;
use App\Repositories\Users\Interfaces\IUserTypeRepository;

class UserTypeRepository extends BaseRepository implements IUserTypeRepository
{
    protected $userType;

    public function __construct(UserTypes $userType)
    {
        parent::__construct($userType);
    }
}
