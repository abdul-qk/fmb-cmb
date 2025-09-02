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
      Schema::table('events', function (Blueprint $table) {
        $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->after('description');
        $table->foreignId('approved_by')->nullable()->constrained('users')->after("deleted_by");
        $table->foreignId('rejected_by')->nullable()->constrained('users')->after("approved_by");
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
          $table->dropColumn('status');
          $table->dropForeign(['approved_by']);
          $table->dropForeign(['rejected_by']);
          $table->dropColumn('approved_by');
          $table->dropColumn('rejected_by');
        });
    }
};
