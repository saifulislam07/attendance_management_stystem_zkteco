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
        // 1. Update Users Table for Sections & Shifts
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('class_id')->constrained('sections')->onDelete('set null');
            $table->string('shift')->nullable()->after('section_id'); // Morning, Day
        });

        // 2. Update Time Tables Table for Advanced Rules
        Schema::table('time_tables', function (Blueprint $table) {
            $table->integer('grace_time')->default(0)->after('out_time'); // minutes
            $table->time('half_day_time')->nullable()->after('grace_time');
            $table->time('overtime_start')->nullable()->after('half_day_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_tables', function (Blueprint $table) {
            $table->dropColumn(['grace_time', 'half_day_time', 'overtime_start']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['section_id', 'shift']);
        });
    }
};
