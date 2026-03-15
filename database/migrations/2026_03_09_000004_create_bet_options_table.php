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
        Schema::create('bet_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('odds', 6, 2);
            $table->boolean('result')->nullable();
            $table->timestamps();

            $table->index('bet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_options');
    }
};
