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
        Schema::create('pet_behavior_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->nullable(false);
            
            $table->string('trait', 128)->nullable(false);
            $table->text('notes')->nullable();

            $table->timestamps();
            
            // Prevent duplicate trait entries for the same pet
            $table->unique(['pet_id', 'trait']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_behavior_traits');
    }
};
