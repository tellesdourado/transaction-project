<?php

namespace Tests\Unit;

use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserTypes;
use App\Repositories\Gateway\TransactionRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserTypeRepository;
use App\Services\Gateway\CreateTransactionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use DatabaseMigrations;

    private $userRepository;
    private $transactionRepository;
    private $userTypeRepository;
    private $userType = array();

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->userRepository        = new UserRepository(new User());
        $this->transactionRepository = new TransactionRepository(new Transaction());
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

    public function testCreateASuccessfullTransaction()
    {
        $data = array(
            "value" => 10.00,
        );


        $payer = $this->userRepository->save(array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        ));

        $data["sender_id"] = $payer->id;

        $payee = $this->userRepository->save(array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        ));

        $data["receiver_id"] = $payee->id;

        $createTransactionService = new CreateTransactionService($this->transactionRepository);

        $this->assertInstanceOf(Transaction::class, $createTransactionService->execute($data));
    }

    public function testTransactionRequiredSenderId()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $payee = $this->userRepository->save($data);

        $data = array(
            "receiver_id" => $payee->id,
            "value"       => 300.00,
        );
        $createTransactionService = new CreateTransactionService($this->transactionRepository);

        try {
            $createTransactionService->execute($data);
        } catch (\Exception $e) {
            $this->assertEquals(422, $e->getCode());
            $this->assertEquals('sender_id field is required.', $e->getMessage());
            $this->assertInstanceOf(DefaultApplicationException::class, $e);
        }
    }

    public function testTransactionRequiredReceiverId()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $payer = $this->userRepository->save($data);

        $data                     = array(
            "sender_id" => $payer->id,
            "value"     => 300.00,
        );
        $createTransactionService = new CreateTransactionService($this->transactionRepository);

        try {
            $createTransactionService->execute($data);
        } catch (\Exception $e) {
            $this->assertEquals(422, $e->getCode());
            $this->assertEquals('receiver_id field is required.', $e->getMessage());
            $this->assertInstanceOf(DefaultApplicationException::class, $e);
        }
    }

    public function testTransactionRequiredValue()
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

        $data = array(
            "sender_id"   => $payer->id,
            "receiver_id" => $payee->id,
        );

        $createTransactionService = new CreateTransactionService($this->transactionRepository);

        try {
            $createTransactionService->execute($data);
        } catch (\Exception $e) {
            $this->assertEquals(422, $e->getCode());
            $this->assertEquals('value field is required.', $e->getMessage());
            $this->assertInstanceOf(DefaultApplicationException::class, $e);
        }
    }
}
