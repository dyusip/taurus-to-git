<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name'           => 'Admin',
            'email'          => 'josef@infozitworks.com',
            'username'       => 'admin',
            'position'       => 'Administrator',
            'password'       => Hash::make('admin'),
            'status'         => 'AC',
            'branch'         => '',
            'gender'         => 'MALE',
            'contact'        => '09434003322',
            'remember_token' => str_random(10)
        ]);
    }
}
