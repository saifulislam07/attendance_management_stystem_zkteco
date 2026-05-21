<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_raw_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_user_id');
            $table->timestamp('punch_time');
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('processed');
            $table->text('error')->nullable();
            $table->timestamps();

            $table->unique(['device_user_id', 'punch_time']);
        });

        Schema::table('sync_logs', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id')->constrained('devices')->nullOnDelete();
            $table->unsignedInteger('total_records')->default(0)->after('last_sync_time');
            $table->unsignedInteger('processed_records')->default(0)->after('total_records');
            $table->unsignedInteger('duplicate_records')->default(0)->after('processed_records');
            $table->unsignedInteger('failed_records')->default(0)->after('duplicate_records');
            $table->text('errors')->nullable()->after('failed_records');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('working_hours', 5, 2)->nullable()->after('early_leave');
            $table->decimal('overtime_hours', 5, 2)->nullable()->after('working_hours');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['working_hours', 'overtime_hours']);
        });

        Schema::table('sync_logs', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn([
                'device_id',
                'total_records',
                'processed_records',
                'duplicate_records',
                'failed_records',
                'errors',
            ]);
        });

        Schema::dropIfExists('attendance_raw_logs');
    }
};
