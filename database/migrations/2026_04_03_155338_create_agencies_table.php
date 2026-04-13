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
        Schema::create('agencies', function (Blueprint $table) {
          $table->id();
          $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
          $table->string('agency_name');
          $table->string('logo')->nullable();
          $table->text('description')->nullable();
          $table->string('website')->nullable();
          $table->decimal('rating', 3, 2)->default(0);
          $table->text('contact_details')->nullable();
          $table->string('business_license')->nullable();
          $table->string('documentation_url')->nullable();
          $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
          $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
