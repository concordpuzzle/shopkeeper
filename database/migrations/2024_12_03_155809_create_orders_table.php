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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('open'); // open, processing, completed, cancelled
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            
            // Shipping Address
            $table->string('shipping_address_line1');
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country')->default('US');
            
            // Order Details
            $table->decimal('total_amount', 10, 2);
            $table->json('options')->nullable(); // Store custom options as JSON
            $table->text('notes')->nullable();
            
            // Timestamps for order tracking
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps(); // creates created_at and updated_at
            $table->softDeletes(); // adds deleted_at for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
