<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserTypes;
use App\Models\Wallet;
use App\Repositories\Gateway\TransactionRepository;
use App\Repositories\Gateway\WalletRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserTypeRepository;
use App\Services\Gateway\CreateTransactionService;
use App\Services\Gateway\RollBackTransactionService;
use App\Services\Users\FindUserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FullTransactionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;



    use DatabaseMigrations;

    private $userRepository;
    private $transactionRepository;
    private $walletRepository;
    private $userType = array();

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->userRepository        = new UserRepository(new User());
        $this->transactionRepository = new TransactionRepository(new Transaction());
        $this->walletRepository      = new WalletRepository(new Wallet());
        $this->userTypeRepository    = new UserTypeRepository(new UserTypes());

        foreach ($this->userTypeRepository->all()->toArray() as $key => $type) {
            if ($type['type'] == 'person') {
                $this->userType["person"] = $type["id"];
            }
            if ($type['type'] == 'store') {
                $this->userType["store"] = $type["id"];
            }
        }
    }

    public function testRollBackTransaction()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $payer = $this->userRepository->save($data);

        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $payee = $this->userRepository->save($data);

        $payerWallet = array(
            "user_id" => $payee->id,
            "balance" => 100,
        );

        $this->walletRepository->save($payerWallet);

        $payeeWallet = array(
            "user_id" => $payee->id,
            "balance" => 100,
        );

        $this->walletRepository->save($payeeWallet);

        $data = array(
            "sender_id"   => $payer->id,
            "receiver_id" => $payee->id,
            "value"       => 10.00,
        );
        $createTransactionService = new CreateTransactionService($this->transactionRepository);
        $transaction = $createTransactionService->execute($data);

        $reversalOperationService = new RollBackTransactionService(
            $this->transactionRepository,
            new FindUserService($this->userRepository)
        );

        $this->assertInstanceOf(Transaction::class, $reversalOperationService->execute($transaction->toArray()));
    }
}
