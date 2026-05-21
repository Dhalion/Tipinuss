<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->foreignUuid('user_bet_id')->nullable()->constrained('user_bets')->nullOnDelete();
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
    }
};
