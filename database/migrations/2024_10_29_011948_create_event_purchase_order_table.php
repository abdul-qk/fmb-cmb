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
        Schema::create('event_purchase_order', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->unique(['event_id', 'purchase_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_purchase_order');
    }
};
