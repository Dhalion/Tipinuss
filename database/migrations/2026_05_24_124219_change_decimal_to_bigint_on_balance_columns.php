<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->bigInteger('soapnuts')->default(1000)->change();
        });

        Schema::table('user_bets', function (Blueprint $table): void {
            $table->bigInteger('amount_wagered')->change();
            $table->bigInteger('potential_winnings')->change();
        });

        Schema::table('balance_transactions', function (Blueprint $table): void {
            $table->bigInteger('amount')->change();
            $table->bigInteger('balance_after')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->decimal('soapnuts', 12, 2)->default(1000)->change();
        });

        Schema::table('user_bets', function (Blueprint $table): void {
            $table->decimal('amount_wagered', 12, 2)->change();
            $table->decimal('potential_winnings', 12, 2)->change();
        });

        Schema::table('balance_transactions', function (Blueprint $table): void {
            $table->decimal('amount', 12, 2)->change();
            $table->decimal('balance_after', 12, 2)->change();
        });
    }
};
