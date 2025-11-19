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
        Schema::create('user_menu_selections', function (Blueprint $table) {
            $table->id();
            $table->string('external_user_id')->comment('External user ID like F0020A');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('dish_id')->constrained('dishes')->onDelete('cascade');
            $table->integer('quantity')->default(0)->comment('0 = skip that day, 1+ = want that many');
            $table->timestamps();
            
            // Prevent duplicate selections for same external user, event, and dish
            $table->unique(['external_user_id', 'event_id', 'dish_id']);
            
            // Index for faster queries
            $table->index(['external_user_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_menu_selections');
    }
};

