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
        Schema::table('daily_checkins', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_checkins', 'date')) {
                $table->date('date')->nullable()->after('stress_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_checkins', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
