<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create([
            'name' => 'Administrator',
            'description' => 'System administrator'
        ]);

        Role::create([
            'name' => 'User',
            'description' => 'System user'
        ]);
    }
}
