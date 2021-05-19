<?php

namespace App\Models;

use App\Models\UserTypes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $fillable = ['full_name', 'email', 'cpf', 'password', 'user_type_id'];

    protected $hidden = ['password'];

    public function type(): HasOne
    {
        return $this->hasOne(
            UserTypes::class,
            'id',
            'user_type_id'
        );
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(
            Wallet::class,
            'user_id',
            'id'
        );
    }
}
