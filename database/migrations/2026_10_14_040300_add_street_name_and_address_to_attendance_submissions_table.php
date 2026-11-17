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
        Schema::table('attendance_submissions', function (Blueprint $table) {
            $table->string('street_name')->nullable()->after('distance_from_school_m');
            $table->text('address')->nullable()->after('street_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_submissions', function (Blueprint $table) {
            $table->dropColumn(['street_name', 'address']);
        });
    }
};
