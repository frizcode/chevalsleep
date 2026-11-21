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
        Schema::create('support_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('epithet')->nullable();
            $table->string('slug')->unique();
            $table->string('card_webp')->nullable();
            $table->enum('rarity', ['SSR', 'SR', 'R'])->default('SSR');
            $table->enum('card_type', ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'])->default('Speed');
            $table->unsignedSmallInteger('specialty_priority')->nullable()->default(50);
            $table->json('key_skills')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_cards');
    }
};
