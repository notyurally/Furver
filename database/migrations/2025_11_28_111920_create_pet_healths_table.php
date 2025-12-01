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
        Schema::create('pet_healths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->nullable(false)->unique(); 
            $table->boolean('is_vaccinated')->default(false)->nullable(false); 
            $table->date('last_vaccinated_date')->nullable();
            $table->boolean('is_spayed')->nullable(); 
            $table->date('last_spay_date')->nullable();
            $table->string('microchip_number', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_healths');
    }
};
