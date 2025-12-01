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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 
            $table->string('name');
            $table->foreignId('breed_id')->nullable()->constrained();
            $table->enum('pet_sex', ['male', 'female']);
            $table->enum('pet_size', ['small', 'medium', 'large', 'extra_large']);
            $table->date('birthdate')->nullable();
            $table->tinyInteger('age')->nullable();
            $table->enum('age_data', ['years', 'months'])->nullable();
            $table->text('description')->nullable();
            $table->decimal('adoption_fee', 10, 2)->nullable();
            $table->string('location');
            $table->enum('pet_status', ['available', 'pending', 'adopted']);
            $table->timestamps();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
