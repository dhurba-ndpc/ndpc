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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ne')->nullable();
            $table->string('slug');
            $table->string('location')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->text('short_description_en')->nullable();
            $table->text('short_description_ne')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('salary')->nullable();
            $table->string('experience_level')->nullable();
            $table->integer('total_applicants')->default(0);
            $table->timestampTz('deadline')->nullable();
            $table->string('external_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
