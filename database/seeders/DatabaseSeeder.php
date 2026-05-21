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

        // 3. Create Bulk Data (100+ Each)

        // Classes & Sections
        $classes = SchoolClass::factory()->count(20)->create();
        $sections = [];
        foreach ($classes as $class) {
            $sections = array_merge($sections, Section::factory()->count(5)->create(['class_id' => $class->id])->all());
        } // 20 Classes * 5 Sections = 100 Sections

        // Users
        $users = User::factory()->count(100)->create()->each(function ($u) use ($sections) {
            if ($u->hasRole('student')) {
                $section = $sections[array_rand($sections)];
                $u->update([
                    'section_id' => $section->id,
                    'class_id'   => $section->class_id,
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
