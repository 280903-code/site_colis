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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->enum('from_country', ['SN', 'KM', 'FR']);
            $table->enum('to_country', ['SN', 'KM', 'FR']);
            $table->date('flight_date');
            $table->string('airline');
            $table->integer('total_kg');
            $table->integer('remaining_kg');
            $table->decimal('price_per_kg', 10, 2);
            $table->enum('currency', ['FCFA', 'EUR']);
            $table->date('drop_off_deadline');
            $table->enum('status', ['open', 'full', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
