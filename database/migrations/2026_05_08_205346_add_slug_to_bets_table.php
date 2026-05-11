<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->string('slug', 255)->after('title')->nullable();
        });

        DB::table('bets')->orderBy('created_at')->lazyById()->each(function (object $bet): void {
            $base = Str::slug($bet->title);
            $slug = $base;
            $counter = 1;

            while (DB::table('bets')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$counter++;
            }

            DB::table('bets')->where('id', $bet->id)->update(['slug' => $slug]);
        });

        Schema::table('bets', function (Blueprint $table) {
            $table->string('slug', 255)->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
