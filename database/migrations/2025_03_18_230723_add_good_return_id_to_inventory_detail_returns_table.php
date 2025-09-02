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
            $table->foreignId('good_return_id')->after("id")->nullable()->constrained('good_returns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_detail_returns', function (Blueprint $table) {
            $table->dropForeign(['good_return_id']);
            $table->dropColumn('good_return_id');
        });
    }
};
