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
        Schema::create('trips', function (Blueprint $table) {
    $table->id();

    $table->string('title');
    $table->text('description');
    $table->string('destination');
    $table->decimal('price', 10, 2);

    $table->integer('duration');
    $table->integer('max_travelers');
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('rating', 3, 2)->default(0);
    $table->foreignId('trip_category_id')->constrained()->cascadeOnDelete();
    $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
    $table->enum('tier', ['basic', 'premium', 'exclusive'])->default('basic');
    $table->enum('status', ['active' , 'inactive'])->default('inactive');
    $table->boolean('featured')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
