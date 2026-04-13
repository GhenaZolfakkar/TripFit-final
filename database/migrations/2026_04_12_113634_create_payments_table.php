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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('EGP');
            $table->string('transaction_ref')->unique();
            $table->string('method')->default('card');
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->enum('refund_status', ['none', 'partial', 'refunded'])->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
