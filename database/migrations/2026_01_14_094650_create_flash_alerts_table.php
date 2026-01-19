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
        Schema::create('flash_alerts', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('type')->default('info'); // info, warning, danger
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->timestamps();
        });

        // Insert a default empty record so we always have one row to update
        DB::table('flash_alerts')->insert([
            'is_active' => false,
            'type' => 'info',
            'title' => 'Titre de l\'alerte',
            'message' => 'Message de l\'alerte...',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_alerts');
    }
};
