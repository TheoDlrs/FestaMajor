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
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->index(['order']); // Used for sorting gallery
            $table->index(['is_featured', 'order']); // Used for filtering featured items
        });

        Schema::table('program_events', function (Blueprint $table) {
            $table->index(['order']); // Used for sorting program
            $table->index(['is_featured']); // Used for checking featured events
        });
        
        Schema::table('flyers', function (Blueprint $table) {
            $table->index(['is_active', 'created_at']); // Used for fetching the active flyer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropIndex(['is_featured', 'order']);
        });

        Schema::table('program_events', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropIndex(['is_featured']);
        });
        
        Schema::table('flyers', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
        });
    }
};