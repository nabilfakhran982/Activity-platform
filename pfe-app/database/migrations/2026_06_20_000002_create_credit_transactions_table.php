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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_credit_id')->constrained('user_credits')->onDelete('cascade');
            $table->enum('type', ['addition', 'deduction']); // purchase or search
            $table->decimal('amount', 10, 2);
            $table->string('reason')->default('search'); // search, purchase, refund, bonus, etc.
            $table->decimal('balance_after', 10, 2);
            $table->json('metadata')->nullable(); // stripe_id, payment_id, etc.
            $table->timestamps();

            $table->index('user_credit_id');
            $table->index('type');
            $table->index('reason');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
