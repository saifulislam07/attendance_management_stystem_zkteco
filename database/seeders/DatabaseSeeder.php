<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceRawLog;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SyncLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // 0. Disable foreign key checks & Truncate Tables
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        User::truncate();
        SchoolClass::truncate();
        Section::truncate();
        Attendance::truncate();
        AttendanceRawLog::truncate();
        SyncLog::truncate();
        Holiday::truncate();
        Leave::truncate();
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Initial Setup (Roles & Permissions)
        $this->call(RolePermissionSeeder::class);

        // 2. Initial Setup (System Settings)
        \App\Models\Setting::updateOrCreate(['key' => 'site_name'], ['value' => 'School Attendance Pro']);
        \App\Models\Setting::updateOrCreate(['key' => 'site_logo'], ['value' => '/logo/logo.png']);
        \App\Models\Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => '/logo/favicon.png']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_text'], ['value' => 'Copyright &copy; ' . date('Y') . ' <a href="#">School Attendance Pro</a>. All rights reserved.']);
        \App\Models\Setting::updateOrCreate(['key' => 'contact_phone'], ['value' => '+880 1234 567890']);
        \App\Models\Setting::updateOrCreate(['key' => 'contact_email'], ['value' => 'info@schoolpro.com']);

        // 2. Create Constant Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'           => 'Super Admin',
                'password'       => Hash::make('111111'),
                'device_user_id' => '1',
                'role'           => 'admin',
            ]
        );
        $admin->assignRole('admin');

        // 3. Create Sample Student
        $sampleClass = SchoolClass::factory()->create(['name' => 'Class 1']);
        $sampleSection = Section::factory()->create([
            'class_id' => $sampleClass->id,
            'name' => 'A',
        ]);

        $student = User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name'           => 'Sample Student',
                'password'       => Hash::make('111111'),
                'device_user_id' => '1001',
                'admission_no'   => 'ADM-1001',
                'role'           => 'student',
                'class_id'       => $sampleClass->id,
                'section_id'     => $sampleSection->id,
                'roll_no'        => '1',
                'date_of_birth'  => now()->subYears(8)->format('Y-m-d'),
                'gender'         => 'Male',
                'blood_group'    => 'B+',
                'shift'          => 'Morning',
                'phone'          => '+8801000000001',
                'guardian_name'  => 'Sample Guardian',
                'guardian_relation' => 'Father',
                'guardian_phone' => '+8801000000002',
                'guardian_email' => 'guardian@example.com',
                'address'        => 'Dhaka, Bangladesh',
            ]
        );
        $student->assignRole('student');

        // 4. Create Bulk Data (100+ Each)

        // Classes & Sections
        $classes = SchoolClass::factory()->count(20)->create();
        $sections = [$sampleSection];
        foreach ($classes as $class) {
            $sections = array_merge($sections, Section::factory()->count(5)->create(['class_id' => $class->id])->all());
        } // 20 Classes * 5 Sections = 100 Sections

        // Users
        $users = User::factory()->count(100)->create()->each(function ($u) use ($sections) {
            $u->assignRole($u->role);

            if ($u->role === 'student') {
                $section = $sections[array_rand($sections)];
                $u->update([
                    'section_id' => $section->id,
                    'class_id'   => $section->class_id,
                    'admission_no' => 'ADM-' . $u->id,
                    'roll_no'    => (string) mt_rand(1, 999),
                    'guardian_name' => fake()->name(),
                    'guardian_relation' => fake()->randomElement(['Father', 'Mother', 'Guardian']),
                    'guardian_phone' => fake()->phoneNumber(),
                ]);
            }
        });

        // Holidays
        Holiday::factory()->count(20)->create();

        // Leaves
        Leave::factory()->count(50)->create();

        // Attendance (Large Volume for reports)
        foreach ($users as $user) {
            // Generate last 15 days of attendance for each user
            for ($i = 0; $i < 15; $i++) {
                $date = now()->subDays($i)->format('Y-m-d');

                // Skip Fridays (General weekend)
                if (date('l', strtotime($date)) == 'Friday') {
                    continue;
                }

                Attendance::updateOrCreate(
                    ['user_id' => $user->id, 'date' => $date],
                    [
                        'check_in'  => '09:' . mt_rand(10, 59) . ':00',
                        'check_out' => '16:' . mt_rand(10, 59) . ':00',
                        'status'    => 'Present',
                    ]
                );
            }
        }

        $this->command->info('Bulk seeding completed successfully (100+ each).');
    }
}
