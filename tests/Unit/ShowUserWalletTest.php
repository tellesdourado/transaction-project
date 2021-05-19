<?php

namespace Tests\Unit;

use App\Exceptions\CustomErrors\DefaultApplicationException;
use App\Models\User;
use App\Models\UserTypes;
use App\Models\Wallet;
use App\Repositories\Gateway\WalletRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserTypeRepository;
use App\Services\Gateway\ShowUserWalletService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowUserWalletTest extends TestCase
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

        $this->userRepository     = new UserRepository(new User());
        $this->walletRepository   = new WalletRepository(new Wallet());
        $this->userTypeRepository = new UserTypeRepository(new UserTypes());
        foreach ($this->userTypeRepository->all()->toArray() as $key => $type) {
            if ($type['type'] == 'person') {
                $this->userType["person"] = $type["id"];
            }
            if ($type['type'] == 'store') {
                $this->userType["store"] = $type["id"];
            }
        }
    }

    public function testShowUserWallet()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $user = $this->userRepository->save($data);

        $userWallet = array(
            "user_id" => $user->id,
            "balance" => 500,
        );

        $this->walletRepository->save($userWallet);

        $data = array(
            "id" => $user->id,
        );

        $showUserWalletService = new ShowUserWalletService($this->userRepository);

        $this->assertIsArray($showUserWalletService->execute($data));
    }


    public function testUserWalletNotExists()
    {
        $data = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $user = $this->userRepository->save($data);

        $data = array(
            "id" => $user->id,
        );

        $showUserWalletService = new ShowUserWalletService($this->userRepository);

        try {
            $showUserWalletService->execute($data);
        } catch (\Exception $e) {
            $this->assertEquals(401, $e->getCode());
            $this->assertEquals('Wallet does not exist.', $e->getMessage());
            $this->assertInstanceOf(DefaultApplicationException::class, $e);
        }
    }
}
