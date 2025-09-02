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
        Schema::table('good_returns', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors');

            // Drop foreign keys before modifying columns
            $table->dropForeign(['event_id']);
            $table->dropForeign(['kitchen_id']);

            // Make event_id and kitchen_id nullable
            $table->foreignId('event_id')->nullable()->change();
            $table->foreignId('kitchen_id')->nullable()->change();

            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('kitchen_id')->references('id')->on('kitchens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_returns', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['event_id']);
            $table->dropForeign(['kitchen_id']);

            // Remove vendor_id column
            $table->dropColumn('vendor_id');

            // Make event_id and kitchen_id NOT NULL again
            $table->foreignId('event_id')->nullable(false)->change();
            $table->foreignId('kitchen_id')->nullable(false)->change();

            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('kitchen_id')->references('id')->on('kitchens');
        });
    }
};
