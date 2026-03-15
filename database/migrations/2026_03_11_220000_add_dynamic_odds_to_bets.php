<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            if (! Schema::hasColumn('bets', 'dynamic_odds_enabled')) {
                $table->boolean('dynamic_odds_enabled')->default(false)->after('status');
            }
            if (! Schema::hasColumn('bets', 'odds_last_updated_at')) {
                $table->timestamp('odds_last_updated_at')->nullable()->after('dynamic_odds_enabled');
            }
        });

        Schema::table('bet_options', function (Blueprint $table) {
            if (! Schema::hasColumn('bet_options', 'base_odds')) {
                $table->decimal('base_odds', 8, 2)->nullable()->after('odds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->dropColumn(['dynamic_odds_enabled', 'odds_last_updated_at']);
        });

        Schema::table('bet_options', function (Blueprint $table) {
            $table->dropColumn('base_odds');
        });
    }
};
