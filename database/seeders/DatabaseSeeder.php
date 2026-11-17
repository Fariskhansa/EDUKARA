<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with deterministic dummy data.
     */
    public function run(): void
    {
        $manifestPath = File::exists(base_path('data_dummy/json/_manifest.json'))
            ? base_path('data_dummy/json/_manifest.json')
            : base_path('../data_dummy/json/_manifest.json');

        if (!File::exists($manifestPath)) {
            $this->command->error("Manifest dummy data tidak ditemukan di: {$manifestPath}");
            return;
        }

        $baseDir = dirname($manifestPath);
        $manifest = json_decode(File::get($manifestPath), true);
        $tables = $manifest['urutan_seed'] ?? [];

        $this->command->info("Memulai seeding data dummy SMA Situraja...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $hashedDefault = Hash::make('Dummy#2026');

        foreach ($tables as $table) {
            $jsonFile = "{$baseDir}/{$table}.json";
            if (!File::exists($jsonFile)) {
                $this->command->warn("File {$table}.json tidak ditemukan, dilewati.");
                continue;
            }

            $rows = json_decode(File::get($jsonFile), true);
            if (!is_array($rows) || empty($rows)) {
                continue;
            }

            $prepared = [];
            foreach ($rows as $row) {
                // Penyesuaian khusus untuk kolom users
                if ($table === 'users') {
                    $passwordDev = $row['password_dev'] ?? null;
                    unset($row['password_dev']);
                    $row['password'] = $passwordDev ? Hash::make($passwordDev) : null;
                }

                // Penyesuaian kolom JSON
                if ($table === 'attendance_periods' && isset($row['active_days']) && is_array($row['active_days'])) {
                    $row['active_days'] = json_encode($row['active_days']);
                }
                if ($table === 'school_settings' && array_key_exists('value', $row)) {
                    $row['value'] = json_encode($row['value']);
                }
                if ($table === 'activity_logs' && isset($row['properties'])) {
                    $row['properties'] = is_array($row['properties']) ? json_encode($row['properties']) : $row['properties'];
                }

                // Pastikan timestamps terisi jika null di beberapa tabel yang membutuhkannya
                if (in_array($table, ['roles', 'permissions', 'academic_years', 'teachers', 'classes', 'students', 'class_enrollments', 'class_officers', 'guardians', 'guardian_student', 'attendance_periods', 'academic_calendar', 'attendance_sessions', 'attendance_submissions', 'attendance_photos', 'attendance_validations', 'school_settings', 'violation_categories', 'student_violations'])) {
                    if (!isset($row['created_at'])) {
                        $row['created_at'] = '2026-07-01 08:00:00';
                    }
                    if (!isset($row['updated_at'])) {
                        $row['updated_at'] = $row['created_at'];
                    }
                }

                $prepared[] = $row;
            }

            // Insert dalam batch agar cepat
            foreach (array_chunk($prepared, 200) as $chunk) {
                DB::table($table)->insert($chunk);
            }

            $count = count($prepared);
            $this->command->info("✓ Tabel {$table}: {$count} data dimasukkan.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Seeding data dummy selesai dengan sukses!");
    }
}
