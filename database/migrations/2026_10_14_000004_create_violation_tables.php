<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->unsignedInteger('default_points')->default(5);
            $table->enum('severity', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('violation_category_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points');
            $table->dateTime('occurred_at');
            $table->text('description')->nullable();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['recorded', 'voided'])->default('recorded');
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_violations');
        Schema::dropIfExists('violation_categories');
    }
};
