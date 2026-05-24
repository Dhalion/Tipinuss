<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bet_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bet_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('odds', 6, 2);
            $table->decimal('base_odds', 6, 2)->nullable();

            $table->boolean('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bet_options');
    }
};
