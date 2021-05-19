<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::table('user_types')->insert(
                [
                    'id'                    => Str::uuid(),
                    'send_authorization'    => false,
                    'receive_authorization' => true,
                    "type"                  => 'store',
                    'created_at'            => Carbon::now('America/Sao_Paulo'),
                    'updated_at'            => Carbon::now('America/Sao_Paulo'),
                ]
            );

            DB::table('user_types')->insert(
                [
                    'id'                     => Str::uuid(),
                    'send_authorization'     => true,
                    'receive_authorization'  => true,
                    "type"                   => 'person',
                    'created_at'             => Carbon::now('America/Sao_Paulo'),
                    'updated_at'             => Carbon::now('America/Sao_Paulo'),
                ]
            );
        } catch (\Throwable $e) {
            Log::info("seed already used");
        }
    }
}
