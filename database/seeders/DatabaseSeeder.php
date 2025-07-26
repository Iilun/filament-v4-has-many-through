<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Through;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Through::factory(10)->create();

        Item::factory(20)->create();
    }
}
