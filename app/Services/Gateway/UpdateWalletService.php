<?php

namespace App\Services\Gateway;

use App\Services\IBaseService;
use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Helpers\ValidateFields;
use App\Repositories\Gateway\Interfaces\IWalletRepository;

class UpdateWalletService implements IBaseService
{
    private $walletRepository;

    public function __construct(IWalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function execute(array $attributes): bool
    {
        $fieldsRequired = ['id'];

        ValidateFields::required($fieldsRequired, $attributes);

        $wallet = $this->walletRepository->findOne($attributes['id']);

        if (!$wallet) {
            throw new DefaultApplicationException('Wallet does not exist.', 401);
        }

        $wallet = $this->walletRepository->update($attributes['id'], $attributes);

        return $wallet;
    }
}
