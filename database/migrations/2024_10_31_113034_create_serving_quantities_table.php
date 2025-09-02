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
    Schema::create('serving_quantities', function (Blueprint $table) {
      $table->id();
      $table->enum('serving', ['Tiffin', 'Thaal']);
      $table->integer('quantity')->nullable();
      $table->integer('serving_person')->nullable();
      $table->date('date_from')->nullable();
      $table->date('date_to')->nullable();
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
    Schema::dropIfExists('serving_quantities');
  }
};
