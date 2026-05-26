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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('subtask_name')->nullable();
        $table->date('subtask_date')->nullable();
        $table->time('subtask_time')->nullable();
        $table->enum('subtask_priority', ['low', 'medium', 'high'])->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
