<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCount = max((int)$this->command->ask('How many [Users] would you like?', 20),1);
        User::factory()->admin()->create();
        User::factory()->john_doe()->create();
        User::factory()->count($userCount - 1)->create();
    }
}
