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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('cm_number')->nullable();
            $table->date('event_date')->nullable();
            $table->string('track_name');
            $table->enum('distance_type', ['Sprint', 'Mile', 'Medium', 'Long']);
            $table->unsignedSmallInteger('distance_meters');
            $table->enum('surface', ['Turf', 'Dirt']);
            $table->enum('season', ['Spring', 'Summer', 'Autumn', 'Winter'])->default('Winter');
            $table->enum('weather', ['Sunny', 'Cloudy', 'Rainy', 'Snowy'])->default('Sunny');
            $table->enum('track_condition', ['Firm', 'Good', 'Soft', 'Heavy'])->default('Firm');
            $table->boolean('is_active')->default(false);
            $table->json('stat_recommendations')->nullable();
            $table->json('skill_inheritance')->nullable();
            $table->json('recommended_skills')->nullable();
            $table->json('supoka_decks')->nullable();
            $table->json('team_compositions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
