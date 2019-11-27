<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'name' => 'Applocum',
            'email' => 'applocumadmin@yopmail.com',
            'password' => Hash::make('Password@123'),
        ]);
    }
}
