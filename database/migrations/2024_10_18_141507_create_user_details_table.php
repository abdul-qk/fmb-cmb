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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('user_id')->constrained('users');
            $table->text('complete_address')->nullable();
            $table->bigInteger('national_identity')->nullable();
            $table->integer('working_designation')->nullable();
            $table->text('responsibilities')->nullable();
            $table->foreignId('education_id')->nullable()->constrained('educations');
            $table->string('status')->nullable();
            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable();
            $table->string('disease')->nullable();
            $table->string('treatment')->nullable();
            $table->integer('no_of_years')->nullable();

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
        Schema::dropIfExists('user_details');
    }
};
