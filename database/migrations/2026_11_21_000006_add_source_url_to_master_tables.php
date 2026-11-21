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
        Schema::table('skills', function (Blueprint $table) {
            $table->string('source_url', 500)->nullable()->after('description');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->string('source_url', 500)->nullable()->after('rarity');
        });

        Schema::table('support_cards', function (Blueprint $table) {
            $table->string('source_url', 500)->nullable()->after('summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('source_url');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('source_url');
        });

        Schema::table('support_cards', function (Blueprint $table) {
            $table->dropColumn('source_url');
        });
    }
};
