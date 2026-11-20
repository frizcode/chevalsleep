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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('variant_title')->nullable();
            $table->string('slug')->unique();
            $table->string('avatar_webp')->nullable();
            $table->enum('default_running_style', ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer']);
            $table->unsignedTinyInteger('rarity')->default(3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
