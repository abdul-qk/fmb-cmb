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
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->float('sub_total', 20, 2)->nullable()->default(0);
            $table->float('per_item_discount', 20, 2)->nullable()->default(0);
            $table->string('discount_option')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
          $table->dropColumn('sub_total');
          $table->dropColumn('per_item_discount');
          $table->dropColumn('discount_option');
        });
    }
};
