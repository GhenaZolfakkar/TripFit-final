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
        Schema::create('agency_requests', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('middle_name')->nullable();
    $table->string('last_name')->nullable();
    $table->string('phone')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->string('email')->unique();
    $table->string('password');

    $table->string('agency_name');
    $table->string('logo')->nullable();
    $table->text('description')->nullable();
    $table->string('website')->nullable();
    $table->decimal('commission_rate', 5, 2)->default(0);
    $table->text('contact_details')->nullable();
    $table->string('business_license')->nullable();
    $table->string('documentation_url')->nullable();

    $table->enum('status', ['pending', 'approved', 'rejected'])
          ->default('pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_requests');
    }
};
