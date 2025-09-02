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
    Schema::create('item_base_uoms', function (Blueprint $table) {
      $table->id();
      $table->foreignId('item_id')->constrained('items');
      $table->foreignId('unit_measure_id')->constrained('unit_measures');
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
    Schema::dropIfExists('item_base_uoms');
  }
};
