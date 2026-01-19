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
        Schema::table('flyers', function (Blueprint $table) {
            $table->string('quote_text')->nullable()->after('description'); // "La force de l'unité."
            $table->string('quote_author')->nullable()->after('quote_text'); // "Devise Castellers"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flyers', function (Blueprint $table) {
            $table->dropColumn(['quote_text', 'quote_author']);
        });
    }
};
