<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypes extends Model
{
    protected $keyType = 'string';
    protected $casts = [
        "send_authorization" => 'boolean',
        "receive_authorization" => 'boolean'
     ];
}
