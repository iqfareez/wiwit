<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSiteSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds. This is useful for demo site instance to populate with
     * fake generated data. It will create a demo user, categories and transactions.
     */
    public function run(): void
    {
        // create user. Same effect as filament add user command
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo-user@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $this->call([
            CategoriesSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
