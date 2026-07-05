<?php

use App\Filament\Imports\TransactionImporter;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;

it('scopes finance resources to the current user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownCategory = Category::create(['user_id' => $user->id, 'name' => 'Food']);
    $otherCategory = Category::create(['user_id' => $otherUser->id, 'name' => 'Travel']);
    $ownTransaction = Transaction::create([
        'user_id' => $user->id,
        'category_id' => $ownCategory->id,
        'amount' => 12.30,
        'transaction_date' => today(),
    ]);
    $otherTransaction = Transaction::create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'amount' => 67.80,
        'transaction_date' => today(),
    ]);

    $this->actingAs($user);

    expect(CategoryResource::getEloquentQuery()->pluck('id')->all())->toBe([$ownCategory->id])
        ->and(TransactionResource::getEloquentQuery()->pluck('id')->all())->toBe([$ownTransaction->id])
        ->and(CategoryResource::getRecordRouteBindingEloquentQuery()->find($otherCategory->id))->toBeNull()
        ->and(TransactionResource::getRecordRouteBindingEloquentQuery()->find($otherTransaction->id))->toBeNull();
});

it('imports categories per user and allows cents', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherCategory = Category::create(['user_id' => $otherUser->id, 'name' => 'Food']);

    Auth::login($user);

    $importer = new TransactionImporter(Import::create([
        'file_name' => 'transactions.csv',
        'file_path' => 'transactions.csv',
        'importer' => TransactionImporter::class,
        'total_rows' => 1,
        'user_id' => $user->id,
    ]), [
        'amount' => 'amount',
        'category' => 'category',
        'notes' => 'notes',
        'transaction_date' => 'transaction_date',
    ], []);

    $importer([
        'amount' => '12.30',
        'category' => 'Food',
        'notes' => 'Lunch',
        'transaction_date' => today()->toDateString(),
    ]);

    $transaction = Transaction::where('user_id', $user->id)->sole();
    $category = $transaction->category;

    expect($transaction->amount)->toBe('12.30')
        ->and($category->user_id)->toBe($user->id)
        ->and($category->id)->not->toBe($otherCategory->id);
});
