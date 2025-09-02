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
        Schema::table('inventory_details', function (Blueprint $table) {
          $table->foreignId('store_id')->after("kitchen_id")->nullable()->constrained('stores');
          $table->foreignId('good_issue_id')->after("id")->nullable()->constrained('good_issues');
          $table->foreignId('select_unit_measure_id')->after("good_issue_id")->nullable()->constrained('unit_measures');
          $table->foreignId('unit_measure_id')->after("select_unit_measure_id")->constrained('unit_measures');
          $table->float('select_quantity', 20, 2)->after("unit_measure_id")->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_details', function (Blueprint $table) {
          $table->dropForeign(['good_issue_id']);
          $table->dropForeign(['select_unit_measure_id']);
          $table->dropForeign(['unit_measure_id']);
          $table->dropColumn('good_issue_id');
          $table->dropColumn('select_unit_measure_id');
          $table->dropColumn('unit_measure_id');
          $table->dropColumn('select_quantity');
        });
    }
};
