<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_product_id')->nullable()->constrained('loan_products');
            $table->foreignId('comaker_id')->nullable()->constrained('users');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_months');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'released', 'rejected', 'completed', 'flagged', 'overdue'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('purpose')->nullable();
            $table->string('ai_tag')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
