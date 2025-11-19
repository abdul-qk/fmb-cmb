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
        Schema::table('user_menu_selections', function (Blueprint $table) {
            $table->date('event_date')->after('event_id')->comment('Date of the event');
            $table->index('event_date'); // Index for faster queries by date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_menu_selections', function (Blueprint $table) {
            $table->dropIndex(['event_date']);
            $table->dropColumn('event_date');
        });
    }
};
