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
        // 1. Classes Table
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('email'); // admin, teacher, student
            $table->string('device_user_id')->unique()->nullable()->after('role');
            $table->foreignId('class_id')->nullable()->constrained('classes')->after('device_user_id');
            $table->string('phone')->nullable()->after('class_id');
        });

        // 3. Time Tables Table
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // teacher, student
            $table->foreignId('class_id')->nullable()->constrained('classes');
            $table->string('day'); // Sunday, Monday, etc.
            $table->time('in_time');
            $table->time('late_time');
            $table->time('out_time');
            $table->timestamps();
        });

        // 4. Attendances Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->default('Absent'); // Present, Late, Absent
            $table->boolean('early_leave')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });

        // 5. Sync Logs Table
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('last_sync_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('time_tables');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['role', 'device_user_id', 'class_id', 'phone']);
        });
        
        Schema::dropIfExists('classes');
    }
};
