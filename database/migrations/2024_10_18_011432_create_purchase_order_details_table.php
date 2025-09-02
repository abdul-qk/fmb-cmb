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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('event_id')->nullable()->constrained('events');
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('select_unit_measure_id')->nullable()->constrained('unit_measures');
            $table->foreignId('unit_measure_id')->constrained('unit_measures');
            $table->float('select_quantity', 20, 2)->nullable();
            $table->float('quantity', 20, 2);
            $table->float('unit_price', 20, 2)->default(0);
            $table->float('total', 20, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
    }
};
