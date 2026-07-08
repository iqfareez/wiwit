<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::exists()) {
            $this->command?->error('No users found. Create a Filament user first.');

            return;
        }

        User::each(function (User $user): void {
            CategoriesSeeder::createFor($user);

            $categoryIds = Category::where('user_id', $user->id)->pluck('id', 'name');
            $expenseCategoryIds = $categoryIds->except('Income');

            // Add income only once
            Transaction::factory()
                ->income()
                ->state([
                    'user_id' => $user->id,
                    'category_id' => $categoryIds['Income'],
                ])
                ->create();

            // and the rest are expenses
            Transaction::factory()
                ->expense()
                ->count(39)
                ->state(fn () => [
                    'user_id' => $user->id,
                    'category_id' => $expenseCategoryIds->random(),
                ])
                ->create();
        });
    }
}
