<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Through;
use App\Models\User;
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

        // Creating two throughs for user 1

        Through::factory(2)->create([
            'user_id' => 1
        ]);

        // Creating other throughs that should not be seen

        Through::factory(2)->create([
            'user_id' => 2
        ]);

        // Creating 6 records that should be seen

        Item::factory(3)->create(
            ['through_id' => 1]
        );
        Item::factory(3)->create(
            ['through_id' => 2]
        );

        // Creating 20 records that should not be seen

        Item::factory(10)->create(
            ['through_id' => 3]
        );
        Item::factory(10)->create(
            ['through_id' => 4]
        );
    }
}
