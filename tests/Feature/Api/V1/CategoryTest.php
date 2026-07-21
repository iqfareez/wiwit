<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('creates updates and lists owned categories', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['view', 'create', 'update']);

    $response = $this->postJson('/api/v1/categories', ['name' => 'Food'])
        ->assertCreated()
        ->assertHeader('Location', 'http://127.0.0.1:8000/api/v1/categories/1')
        ->assertJsonPath('is_active', true);

    $this->patchJson("/api/v1/categories/{$response->json('id')}", ['is_active' => false])
        ->assertOk()
        ->assertJsonPath('is_active', false);

    $this->getJson('/api/v1/categories')->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/categories?show_inactive=1')->assertJsonCount(1, 'data');
});

it('preserves historical transactions when deleting and recreating a category', function () {
    $user = User::factory()->create();
    $category = Category::create(['user_id' => $user->id, 'name' => 'Travel']);
    $transaction = Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Sanctum::actingAs($user, ['view', 'delete', 'create']);

    $this->deleteJson("/api/v1/categories/{$category->id}")->assertNoContent();
    $this->getJson("/api/v1/transactions/{$transaction->id}")
        ->assertJsonPath('category.name', 'Travel')
        ->assertJsonMissingPath('links.category');

    $this->postJson('/api/v1/categories', ['name' => 'Travel'])->assertCreated();
});

it('returns category conflicts and invalid body problem details', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['create']);
    Category::create(['user_id' => $user->id, 'name' => 'Food']);

    $this->postJson('/api/v1/categories', ['name' => 'Food'])
        ->assertConflict()
        ->assertJsonPath('type', '/problems/conflict');

    $this->call('POST', '/api/v1/categories', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'text/plain',
    ], '{}')->assertStatus(415)->assertJsonPath('type', '/problems/unsupported-media-type');

    $this->call('POST', '/api/v1/categories', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ], '{')->assertBadRequest()->assertJsonPath('type', '/problems/malformed-json');
});
