<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agency_routes', function (Blueprint $table) {
            $table->string('from_country', 100)->change();
            $table->string('to_country', 100)->change();
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->string('from_country', 100)->change();
            $table->string('to_country', 100)->change();
            $table->string('currency', 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $allowedCountries = ['SN', 'KM', 'FR'];

        foreach (['agency_routes', 'flights'] as $tableName) {
            foreach (['from_country', 'to_country'] as $column) {
                if (DB::table($tableName)->whereNotIn($column, $allowedCountries)->exists()) {
                    throw new RuntimeException('Cannot restore the restricted country list while international country values exist.');
                }
            }
        }

        if (DB::table('flights')->whereNotIn('currency', ['FCFA', 'EUR'])->exists()) {
            throw new RuntimeException('Cannot restore the restricted currencies while other currency values exist.');
        }

        Schema::table('agency_routes', function (Blueprint $table) {
            $table->enum('from_country', ['SN', 'KM', 'FR'])->change();
            $table->enum('to_country', ['SN', 'KM', 'FR'])->change();
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->enum('from_country', ['SN', 'KM', 'FR'])->change();
            $table->enum('to_country', ['SN', 'KM', 'FR'])->change();
            $table->enum('currency', ['FCFA', 'EUR'])->change();
        });
    }
};
