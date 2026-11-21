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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon_webp')->nullable();
            $table->enum('rarity', ['Normal', 'Rare', 'Unique', 'Inherited'])->default('Normal');
            $table->enum('skill_type', ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'])->default('Velocity');
            $table->enum('running_style', ['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'])->default('Universal');
            $table->enum('distance_type', ['Universal', 'Sprint', 'Mile', 'Medium', 'Long'])->default('Universal');
            $table->text('activation_condition')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
