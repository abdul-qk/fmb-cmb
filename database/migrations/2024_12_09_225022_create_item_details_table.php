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
        Schema::create('item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->float('received_quantity', 20, 2);
            $table->float('issued_quantity', 20, 2)->default(0);
            $table->float('returned_quantity', 20, 2)->default(0);
            $table->float('supplier_returned_quantity', 20, 2)->default(0);
            $table->float('adjusted_quantity', 20, 2)->default(0);
            $table->float('available_quantity', 20, 2);
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
        Schema::dropIfExists('item_details');
    }
};
