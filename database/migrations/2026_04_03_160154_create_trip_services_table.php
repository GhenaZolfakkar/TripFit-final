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
        Schema::create('trip_services', function (Blueprint $table) {
           $table->id();
$table->foreignId('trip_id')->constrained()->cascadeOnDelete();
$table->string('service_name');
$table->enum('type', ['included','not_included']);
$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_services');
    }
};
