<?php

use App\Filament\Imports\TransactionImporter;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Transactions\Pages\ManageTransactions;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

it('scopes finance resources to the current user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownCategory = Category::create(['user_id' => $user->id, 'name' => 'Food']);
    $otherCategory = Category::create(['user_id' => $otherUser->id, 'name' => 'Travel']);
    $ownTransaction = Transaction::create([
        'user_id' => $user->id,
        'category_id' => $ownCategory->id,
        'title' => 'Lunch',
        'type' => 'expense',
        'amount' => 12.30,
        'transaction_date' => today(),
    ]);
    $otherTransaction = Transaction::create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'title' => 'Flight',
        'type' => 'expense',
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
        'total_rows' => 2,
        'user_id' => $user->id,
    ]), [
        'amount' => 'amount',
        'category' => 'category',
        'title' => 'title',
        'notes' => 'notes',
        'transaction_date' => 'transaction_date',
    ], []);

    $importer([
        'amount' => '-12.30',
        'category' => 'Food',
        'title' => 'Lunch',
        'notes' => 'Lunch',
        'transaction_date' => today()->toDateString(),
    ]);
    $importer([
        'amount' => '12.30',
        'category' => 'Food',
        'title' => 'Paycheck',
        'notes' => 'Paycheck',
        'transaction_date' => today()->toDateString(),
    ]);

    $expense = Transaction::where('user_id', $user->id)->where('title', 'Lunch')->sole();
    $income = Transaction::where('user_id', $user->id)->where('title', 'Paycheck')->sole();
    $category = $expense->category;

    expect($expense->amount)->toBe('12.30')
        ->and($expense->type)->toBe('expense')
        ->and($income->amount)->toBe('12.30')
        ->and($income->type)->toBe('income')
        ->and($category->user_id)->toBe($user->id)
        ->and($category->id)->not->toBe($otherCategory->id);
});

it('shows transaction amounts with type direction', function () {
    $user = User::factory()->create();

    Transaction::create([
        'user_id' => $user->id,
        'title' => 'Lunch',
        'type' => 'expense',
        'amount' => 12.30,
        'transaction_date' => today(),
    ]);
    Transaction::create([
        'user_id' => $user->id,
        'title' => 'Paycheck',
        'type' => 'income',
        'amount' => 12.30,
        'transaction_date' => today(),
    ]);

    $this->actingAs($user);

    Livewire::test(ManageTransactions::class)
        ->assertSee('-12.30')
        ->assertSee('+12.30');
});
