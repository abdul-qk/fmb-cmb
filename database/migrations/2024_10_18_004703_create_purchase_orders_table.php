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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->foreignId('place_id')->constrained('places');
            $table->float('amount', 20, 2)->default(0);
            $table->float('additional_charges', 20, 2)->default(0);
            $table->float('discount', 20, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('type')->default('po');
            $table->string('file_name')->nullable();
            $table->string('approved_file_name')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
