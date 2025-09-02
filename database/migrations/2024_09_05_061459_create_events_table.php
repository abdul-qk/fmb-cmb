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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('place_id')->nullable()->constrained('places');
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->string('event_hours');
            $table->string('meal');
            $table->string('serving');
            $table->integer('serving_persons')->nullable();
            $table->integer('no_of_thaal')->nullable();
            $table->text('description')->nullable();
            $table->integer('host_its_no')->nullable();
            $table->integer('host_sabeel_no')->nullable();
            $table->string('host_name')->nullable();
            $table->text('host_menu')->nullable();
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
        Schema::dropIfExists('events');
    }
};
