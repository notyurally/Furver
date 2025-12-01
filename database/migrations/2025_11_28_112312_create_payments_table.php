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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('adoption_applications')->nullable(false);
            
            // Based on ERD: method varchar(255) NN and payment_method_E (Enum) NN
            $table->string('method', 255)->nullable(false); 
            $table->enum('payment_method', ['CreditCard', 'Cash', 'Transfer'])->nullable(false);
            
            $table->string('receipt_path', 1024)->nullable(); 
            
            $table->enum('delivery_option', ['Pickup', 'Delivery'])->nullable(false);

            $table->decimal('delivery_fee', 10, 2)->default(0.00)->nullable(false); 
            $table->decimal('total_amount', 10, 2)->nullable(false); 
            
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed', 'Refunded'])->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
