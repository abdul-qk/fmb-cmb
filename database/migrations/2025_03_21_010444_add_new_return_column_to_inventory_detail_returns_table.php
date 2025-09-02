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
        Schema::table('inventory_detail_returns', function (Blueprint $table) {
            $table->foreignId('select_unit_measure_id')->nullable()->after('quantity')->constrained('unit_measures');
            $table->float('select_quantity', 20, 2)->nullable()->after('select_unit_measure_id');
            $table->string('reason')->nullable()->after('select_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_detail_returns', function (Blueprint $table) {
            $table->dropForeign(['select_unit_measure_id']);
            $table->dropColumn('select_unit_measure_id');
            $table->dropColumn('select_quantity');
            $table->dropColumn('reason');
        });
    }
};
