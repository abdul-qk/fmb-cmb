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
        Schema::table('purchase_orders', function (Blueprint $table) {
          $table->foreignId('store_id')->nullable()->constrained('stores');
          $table->date('grn_date')->nullable()->after("store_id");
          $table->foreignId('paid_by')->nullable()->constrained('users')->after("vendor_id");
          $table->string('bill_no')->nullable()->after("grn_date");
          $table->string('file_path')->nullable()->after("bill_no")->nullable();
          $table->float('sub_amount', 20, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
          $table->dropForeign(['store_id']);
          $table->dropForeign(['paid_by']);
          $table->dropColumn('store_id');
          $table->dropColumn('grn_date');
          $table->dropColumn('paid_by');
          $table->dropColumn('bill_no');
          $table->dropColumn('file_path');
          $table->dropColumn('sub_amount');
        });
    }
};
