<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('roll_no')->nullable()->after('section_id');
            $table->index(['class_id', 'section_id', 'roll_no']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'section_id', 'roll_no']);
            $table->dropColumn('roll_no');
        });
    }
};
