<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        // Subtask 2
        $table->string('subtask2_name')->nullable()->after('subtask_priority');
        $table->date('subtask2_date')->nullable()->after('subtask2_name');
        $table->time('subtask2_time')->nullable()->after('subtask2_date');
        $table->enum('subtask2_priority', ['low', 'medium', 'high'])->nullable()->after('subtask2_time');
        // Subtask 3
        $table->string('subtask3_name')->nullable()->after('subtask2_priority');
        $table->date('subtask3_date')->nullable()->after('subtask3_name');
        $table->time('subtask3_time')->nullable()->after('subtask3_date');
        $table->enum('subtask3_priority', ['low', 'medium', 'high'])->nullable()->after('subtask3_time');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['subtask2_name','subtask2_date','subtask2_time','subtask2_priority',
                            'subtask3_name','subtask3_date','subtask3_time','subtask3_priority']);
    });
}

    /**
     * Reverse the migrations.
     */
    
};
