<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public const DEFAULT_CATEGORIES = [
        'Food & Drinks',
        'Transport',
        'Utilities',
        'Groceries',
        'Health',
        'Entertainment',
        'Income',
        'Fuel',
        'Other',
    ];

    public static function createFor(User $user): void
    {
        foreach (self::DEFAULT_CATEGORIES as $name) {
            Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $name,
                ],
                [
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::exists()) {
            $this->command?->error('No users found. Create a Filament user first.');

            return;
        }

        User::each(fn (User $user) => self::createFor($user));
    }
}
