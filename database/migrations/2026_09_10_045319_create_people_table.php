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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index();
            $table->string('name_en');
            $table->string('name_ne')->nullable();
            $table->string('designation_en')->nullable();
            $table->string('designation_ne')->nullable();
            $table->string('organization_en')->nullable();
            $table->string('organization_ne')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->jsonb('meta_json')->nullable();
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
        Schema::dropIfExists('people');
    }
};
