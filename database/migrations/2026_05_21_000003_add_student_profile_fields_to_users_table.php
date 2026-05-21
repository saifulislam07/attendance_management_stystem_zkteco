<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admission_no')->nullable()->unique()->after('device_user_id');
            $table->date('date_of_birth')->nullable()->after('roll_no');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('blood_group')->nullable()->after('gender');
            $table->string('guardian_name')->nullable()->after('phone');
            $table->string('guardian_relation')->nullable()->after('guardian_name');
            $table->string('guardian_phone')->nullable()->after('guardian_relation');
            $table->string('guardian_email')->nullable()->after('guardian_phone');
            $table->text('address')->nullable()->after('guardian_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['admission_no']);
            $table->dropColumn([
                'admission_no',
                'date_of_birth',
                'gender',
                'blood_group',
                'guardian_name',
                'guardian_relation',
                'guardian_phone',
                'guardian_email',
                'address',
            ]);
        });
    }
};
