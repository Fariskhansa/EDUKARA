<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revoke_reason')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'is_active']);
        });

        Schema::create('attendance_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->time('opens_at');
            $table->time('closes_at');
            $table->time('late_after')->nullable();
            $table->json('active_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_calendar', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->enum('type', ['libur', 'ujian', 'kegiatan'])->default('libur');
            $table->string('description');
            $table->boolean('is_school_day')->default(false);
            $table->timestamps();
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_period_id')->constrained()->cascadeOnDelete();
            $table->date('session_date');
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
            $table->dateTime('late_after')->nullable();
            $table->enum('status', ['scheduled', 'open', 'closed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->unique(['attendance_period_id', 'session_date'], 'att_session_period_date_unique');
            $table->index(['session_date', 'status']);
        });

        Schema::create('attendance_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_qr_code_id')->constrained()->cascadeOnDelete();
            $table->timestamp('submitted_at')->useCurrent();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('gps_accuracy_m', 8, 2)->nullable();
            $table->timestamp('gps_captured_at_client')->nullable();
            $table->decimal('distance_from_school_m', 8, 2)->nullable();
            $table->enum('geofence_flag', ['inside', 'outside', 'not_configured'])->default('not_configured');
            $table->boolean('is_late')->default(false);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->unsignedInteger('lock_version')->default(0);
            $table->string('idempotency_key', 64)->unique();
            $table->timestamp('superseded_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for quick lookups and concurrency
            $table->index(['status', 'attendance_session_id'], 'att_sub_status_session_idx');
            $table->index(['class_id', 'attendance_session_id', 'status'], 'att_sub_class_session_status_idx');
            $table->index(['student_id', 'attendance_session_id'], 'att_sub_student_session_idx');
            $table->index(['submitted_at']);
        });

        Schema::create('attendance_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_submission_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('disk')->default('private');
            $table->string('path')->unique();
            $table->string('mime_type', 50)->default('image/jpeg');
            $table->unsignedInteger('size_bytes');
            $table->unsignedSmallInteger('width')->default(1280);
            $table->unsignedSmallInteger('height')->default(960);
            $table->char('sha256', 64)->index();
            $table->date('retention_until')->nullable()->index();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('validator_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['accept', 'reject', 'revise']);
            $table->enum('from_status', ['pending', 'accepted', 'rejected']);
            $table->enum('to_status', ['pending', 'accepted', 'rejected']);
            $table->text('reason')->nullable();
            $table->timestamp('decided_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['attendance_submission_id', 'decided_at'], 'att_val_submission_decided_idx');
            $table->index(['validator_id', 'decided_at'], 'att_val_validator_decided_idx');
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_dummy_value')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('attendance_validations');
        Schema::dropIfExists('attendance_photos');
        Schema::dropIfExists('attendance_submissions');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('academic_calendar');
        Schema::dropIfExists('attendance_periods');
        Schema::dropIfExists('class_qr_codes');
    }
};
