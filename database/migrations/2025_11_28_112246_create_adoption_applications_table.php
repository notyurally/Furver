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
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable(false);
            $table->foreignId('pet_id')->constrained('pets')->nullable(false);
            $table->string('mobile_number', 20);
            $table->text('address');
            $table->longText('description');
            $table->date('application_date')->nullable(false);
            $table->enum('application_status', ['Pending', 'Approved', 'Rejected', 'Withdrawn'])->nullable(false);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_histories');
        Schema::dropIfExists('adoption_applications');
    }
};
