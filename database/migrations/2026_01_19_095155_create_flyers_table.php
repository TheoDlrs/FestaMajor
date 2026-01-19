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
        Schema::create('flyers', function (Blueprint $row) {
            $row->id();
            $row->string('title'); // Internal name
            $row->string('image_url');
            
            // Content for the Homepage Section
            $row->string('subtitle')->nullable(); // "L'Héritage"
            $row->string('headline')->nullable(); // "Terre de Feu & d'Or"
            $row->text('description')->nullable(); // Paragraph text
            
            $row->boolean('is_active')->default(true);
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flyers');
    }
};
