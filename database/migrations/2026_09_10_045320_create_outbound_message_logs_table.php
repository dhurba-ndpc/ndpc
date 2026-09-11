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
        Schema::create('outbound_message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message_type', 50);
            $table->string('source_type', 50);
            $table->unsignedBigInteger('source_id');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_message_logs');
    }
};
