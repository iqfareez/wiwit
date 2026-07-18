<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. This is to reverse the migration 2025_11_25_193022_create_categories_table.php
     * that adds index for user_id and category name.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['user_id', 'name']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
