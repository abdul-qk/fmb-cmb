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
    Schema::create('uom_conversions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('base_uom')->constrained('unit_measures');
      $table->float('conversion_value', 20, 2);
      $table->foreignId('secondary_uom')->constrained('unit_measures');
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
    Schema::dropIfExists('uom_conversions');
  }
};
