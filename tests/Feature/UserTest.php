<?php

namespace Tests\Feature;

use App\Models\UserTypes;
use App\Repositories\Users\UserTypeRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function testUserCreate()
    {
        $payload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'exemple@exemple.com',
            'cpf'        => '99999999999',
            'password'   => '123456',
            'user_type_id' => $this->userType["person"]
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(200);
    }

    public function testUserTypeIdIncorret()
    {
        $payload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'exemple1@exemple.com',
            'cpf'        => '99999999998',
            'password'   => '123456',
            'user_type_id' => '0000'
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'user_type_id' => ['The selected user type id is invalid.'],
                ]
            );
    }

    public function testUserRequiredFullName()
    {
        $payload = [
            'email'      => 'exemple1@exemple.com',
            'cpf'        => '99999999998',
            'password'   => '123456',
            'user_type_id' => '0000'
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'full_name' => ['The full name field is required.'],
                ]
            );
    }

    public function testUserRequiredCpf()
    {
        $payload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'exemple1@exemple.com',
            'password'   => '123456',
            'user_type_id' => '0000'
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'cpf' => ['The cpf field is required.'],
                ]
            );
    }

    public function testUserRequiredEmail()
    {
        $payload = [
            'full_name'  => $this->faker->name(),
            'cpf'        => '99999999998',
            'password'   => '123456',
            'user_type_id' => '0000'
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'email' => ['The email field is required.'],
                ]
            );
    }

    public function testUserRequiredPassword()
    {
        $payload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'exemple2@exemple.com',
            'cpf'        => '99999999998',
            'user_type_id' => '0000'
        ];

        $this->postJson('/api/users', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'password' => ['The password field is required.'],
                ]
            );
    }

    public function testUserCpfIsUsed()
    {
        $firstPayload = [
            'full_name'  => $this->faker->name(),
            'email'      => $this->faker->email,
            'cpf'        => '12345',
            'password'   => $this->faker->password(7, 10),
            'user_type_id' => $this->userType["person"]
        ];

        $secondPayload = [
            'full_name'  => $this->faker->name(),
            'email'      => $this->faker->email,
            'cpf'        => '12345',
            'password'   =>  $this->faker->password(7, 10),
            'user_type_id' => $this->userType["person"]
        ];

        $this->postJson('/api/users', $firstPayload);
        $this->postJson('/api/users', $secondPayload)
            ->assertStatus(422)->assertJson(
                [
                    'cpf' => ['The cpf has already been taken.'],
                ]
            );
    }


    public function testUserEmailIsUsed()
    {
        $firstPayload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'repetedEmail@exemple.com',
            'cpf'        => $this->faker->numberBetween(1000),
            'password'   => $this->faker->password(7, 10),
            'user_type_id' => $this->userType["person"]
        ];

        $secondPayload = [
            'full_name'  => $this->faker->name(),
            'email'      => 'repetedEmail@exemple.com',
            'cpf'        => $this->faker->numberBetween(1000),
            'password'   => $this->faker->password(7, 10),
            'user_type_id' => $this->userType["person"]
        ];

        $this->postJson('/api/users', $firstPayload);
        $this->postJson('/api/users', $secondPayload)
            ->assertStatus(422)->assertJson(
                [
                    'email' => ['The email has already been taken.'],
                ]
            );
    }
}
