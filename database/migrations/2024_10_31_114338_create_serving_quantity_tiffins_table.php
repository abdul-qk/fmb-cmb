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
    Schema::create('serving_quantity_tiffins', function (Blueprint $table) {
      $table->id();
      $table->foreignId('serving_quantity_id')->constrained('serving_quantities');
      $table->foreignId('tiffin_size_id')->constrained('tiffin_sizes');
      $table->integer('quantity');
      $table->date('date_from');
      $table->date('date_to');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('serving_quantity_tiffins');
  }
};
