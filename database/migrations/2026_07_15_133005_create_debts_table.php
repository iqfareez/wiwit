<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('other_person')->comment('other person of interest');
            $table->string('direction')->comment('Whether I borrowed or lent the money');
            $table->decimal('amount', 15, 2);
            $table->date('borrowed_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'borrowed_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
