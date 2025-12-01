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
        Schema::create('pet_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->nullable(false);
            $table->string('photo_path', 1024)->nullable(false)->comment('The path to the main photo.'); 
            $table->json('additional_photos')->nullable(); 
            $table->boolean('is_profile')->default(false)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_phots');
    }
};
