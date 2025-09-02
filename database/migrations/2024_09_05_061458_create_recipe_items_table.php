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
        Schema::create('recipe_items', function (Blueprint $table) {
          $table->id();
          $table->foreignId('recipe_id')->constrained('recipes');
          $table->foreignId('item_id')->constrained('items');
          $table->float('select_item_quantity', 20, 2)->nullable();
          $table->float('item_quantity', 20, 2)->nullable();
          $table->foreignId('select_measurement_id')->constrained('unit_measures');
          $table->foreignId('measurement_id')->constrained('unit_measures');
          $table->text('description')->nullable();
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
