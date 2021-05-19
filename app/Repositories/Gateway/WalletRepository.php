<?php

namespace App\Repositories\Gateway;

use App\Repositories\BaseRepository;
use App\Models\Wallet;
use App\Repositories\Gateway\Interfaces\IWalletRepository;

class WalletRepository extends BaseRepository implements IWalletRepository
{
    protected $wallet;

    public function __construct(Wallet $wallet)
    {
        parent::__construct($wallet);
    }
}
