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
          $table->foreignId('return_by')->constrained('users')->after("inventory_detail_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_detail_returns', function (Blueprint $table) {
          $table->dropForeign(['return_by']);
          $table->dropColumn('return_by');
        });
    }
};
