<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($index = 1 ; $index < 4; $index++) {
            User::create([
                'username' => "user$index",
                'email' => "user$index@gmail.com",
                'password' => bcrypt('Aa123456'),
                'email_verified_at' => Carbon::now(config('app.timezone')),
                'active' => true
            ]);
        }
    }
}
