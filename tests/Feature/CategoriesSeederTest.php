<?php

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategoriesSeeder;

it('asks for a filament user before seeding categories', function () {
    $this->artisan('db:seed', ['--class' => CategoriesSeeder::class])
        ->expectsOutputToContain('No users found. Create a Filament user first.')
        ->assertSuccessful();

    expect(Category::count())->toBe(0);
});

it('creates default categories for existing users once', function () {
    $user = User::factory()->create();

    $this->artisan('db:seed', ['--class' => CategoriesSeeder::class])
        ->assertSuccessful();

    expect(Category::where('user_id', $user->id)->orderBy('id')->pluck('name')->all())
        ->toEqual(CategoriesSeeder::DEFAULT_CATEGORIES)
        ->and(Category::where('user_id', $user->id)->where('is_active', true)->count())
        ->toBe(count(CategoriesSeeder::DEFAULT_CATEGORIES));

    CategoriesSeeder::createFor($user);

    expect(Category::where('user_id', $user->id)->count())->toBe(count(CategoriesSeeder::DEFAULT_CATEGORIES));
});
