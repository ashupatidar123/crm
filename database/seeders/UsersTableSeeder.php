<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 100) as $index) {
            $name = str_shuffle('RamShyam');
            User::insert([
                'name' => $name,
                'email' => strtolower($name).'@gmail.com',
                'password' => bcrypt('password'),
                'mobile' =>rand(1111111111,9999999999),
                'amount' =>rand(111,999),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
