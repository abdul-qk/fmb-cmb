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
        Schema::create('vendor_banks', function (Blueprint $table) {
          $table->id();
          $table->foreignId('vendor_id')->constrained('vendors');
          $table->string('ntn')->nullable();
          $table->string('bank');
          $table->string('bank_title');
          $table->string('bank_address');
          $table->string('account_no');
          $table->string('bank_branch');
          
          $table->foreignId('created_by')->constrained('users');
          $table->foreignId('updated_by')->nullable()->constrained('users');
          $table->foreignId('deleted_by')->nullable()->constrained('users');
          $table->enum('primary', [0, 1])->default(0);
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_banks');
    }
};
