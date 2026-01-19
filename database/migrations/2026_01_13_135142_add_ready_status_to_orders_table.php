<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Status can now be: confirmed, ready, paid
        // No change needed to column type if it's string, but good to document.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
