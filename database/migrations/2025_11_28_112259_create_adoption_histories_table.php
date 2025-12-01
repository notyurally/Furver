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
        Schema::create('adoption_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable(false);
            $table->foreignId('pet_id')->constrained('pets')->nullable(false)->unique(); // A pet can only be adopted once
            $table->foreignId('application_id')->constrained('adoption_applications')->nullable(false)->unique(); // One history record per successful application
            
            $table->timestamp('adopted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_histories');
    }
};
