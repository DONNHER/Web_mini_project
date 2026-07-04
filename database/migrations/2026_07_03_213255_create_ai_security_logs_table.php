<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('feature'); // e.g., 'fraud_detection'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('resource_type')->nullable(); // e.g., 'Order'
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->integer('risk_score')->nullable();
            $table->string('risk_category')->nullable();
            $table->text('reason')->nullable();
            $table->string('provider'); // 'gemini', 'openai', 'ollama'
            $table->float('response_time_ms');
            $table->json('input_context'); // The context sent to AI
            $table->timestamps();

            $table->index(['resource_type', 'resource_id']);
            $table->index('risk_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_security_logs');
    }
};
