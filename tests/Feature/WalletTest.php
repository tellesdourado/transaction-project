<?php

namespace Tests\Feature;

use App\Models\UserTypes;
use App\Repositories\Users\UserTypeRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use DatabaseMigrations;
    private $userType = array();

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
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

    public function testCreateUserWallet()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $user    = json_decode($content);

        $this->getJson('/api/wallet/' . $user->id)
            ->assertStatus(200);
    }

    public function testCheckUserIdIsExistesOrIsInvalid()
    {
        $id = $this->faker->uuid;
        $this->getJson('/api/wallet/' . $id)
            ->assertStatus(422)
            ->assertJson(
                [
                    "id" => ["The selected id is invalid."]
                ]
            );
    }
}
