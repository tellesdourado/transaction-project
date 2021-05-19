<?php

namespace Tests\Feature;

use App\Models\UserTypes;
use App\Repositories\Users\UserTypeRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FullTransactionTest extends TestCase
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

    public function testCreateNewFullTransaction()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => '1.50',
            'payer' => $payer->id,
            'payee' => $payee->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(200);
    }

    public function testNeedToDefineAValue()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'value' => ['The value field is required.'],
                ]
            );
    }

    public function testFullTransactionRequiredSender()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => '100.00',
            'payee' => $payee->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'payer' => ['The payer field is required.'],
                ]
            );
    }

    public function testFullTransactionRequiredReceiver()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = [
            'value' => '100.00',
            'payer' => $payer->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'payee' => ['The payee field is required.'],
                ]
            );
    }


    public function testSenderNotExists()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => 10.00,
            'payer' => $this->faker->uuid,
            'payee' => $payee->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'payer' => ['The selected payer is invalid.'],
                ]
            );
    }

    public function testReceiverNotExists()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = [
            'value' => 10.00,
            'payee' => $this->faker->uuid,
            'payer' => $payer->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'payee' => ['The selected payee is invalid.'],
                ]
            );
    }


    public function testInvalidValueError()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );


        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => $this->faker->name,
            'payee' => $payee->id,
            'payer' => $payer->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(422)
            ->assertJson(
                [
                    'value' => ['this is not a real value.'],
                ]
            );
    }

    public function testUserCannotSendMoney()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["store"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["store"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => 10.00,
            'payee' => $payee->id,
            'payer' => $payer->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(401)
            ->assertJson(
                [
                    'message' => 'This user cannot do a transaction',
                ]
            );
    }

    public function testWalletDoesNotHaveSuficientBalance()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );


        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => 10000.00,
            'payee' => $payee->id,
            'payer' => $payer->id,
        ];

        $this->postJson('/api/transaction', $payload)
            ->assertStatus(401)
            ->assertJson(
                [
                    'message' => 'Insufficient funds to proceed.',
                ]
            );
    }

    public function testRollBackTransaction()
    {
        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payer   = json_decode($content);

        $payload = array(
            "full_name"  => $this->faker->name(),
            "email"      => $this->faker->email,
            "cpf"        => $this->faker->numberBetween(1000),
            "password"   => $this->faker->password(7, 10),
            "user_type_id" => $this->userType["person"]
        );

        $content = $this->postJson('/api/users', $payload)->getContent();
        $payee   = json_decode($content);

        $payload = [
            'value' => 40.00,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ];

        $content = $this->postJson('/api/transaction', $payload)->getContent();

        $transaction = json_decode($content);

        $this->postJson('/api/transaction/' . $transaction->id . '/rollback')
            ->assertStatus(200);
    }

    public function testRollBackFalseTransactionId()
    {
        $id = $this->faker->uuid;

        $this->postJson('/api/transaction/' . $id . '/rollback')
            ->assertStatus(422)
            ->assertJson(
                [
                    'id' => ['The selected id is invalid.'],
                ]
            );
    }
}
