<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserTypes;
use App\Models\Wallet;
use App\Repositories\Gateway\WalletRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserTypeRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbleToTransactionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use DatabaseMigrations;

    private $userRepository;
    private $walletRepository;
    private $userType = array();

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->userRepository   = new UserRepository(new User());
        $this->walletRepository = new WalletRepository(new Wallet());
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

    public function testUserCanSendMoney()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $user = $this->userRepository->save($data);
        $user->load("type");

        $this->assertTrue($user->type->send_authorization);
    }

    public function testUserCannotSendMoney()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["store"]
        );

        $user = $this->userRepository->save($data);
        $user->load("type");
        $this->assertFalse($user->type->send_authorization);
    }
}
