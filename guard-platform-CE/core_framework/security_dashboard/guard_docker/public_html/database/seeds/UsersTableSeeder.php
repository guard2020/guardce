<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Minds & Sparks',
            'email' => 'admin@mindsandsparks.org',
            'role_id' => 1,
            'status' => 1,
            'password' => Hash::make('m&s2021'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Pipeline System Operator',
            'email' => 'pipeline.operator@mindsandsparks.org',
            'role_id' => 2,
            'status' => 1,
            'password' => Hash::make('operator2021m&s'),
            'remember_token' => Str::random(10)
        ]);
    }
}