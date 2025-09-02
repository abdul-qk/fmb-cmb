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
        Schema::create('item_base_uom_unit_measure', function (Blueprint $table) {
          $table->foreignId('item_base_uom_id')->constrained('item_base_uoms');
          $table->foreignId('secondary_uom')->constrained('unit_measures');
          $table->unique(['item_base_uom_id', 'secondary_uom'], 'unique_item_base_uom_secondary_uom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('item_base_uom_unit_measure');
    }
};
