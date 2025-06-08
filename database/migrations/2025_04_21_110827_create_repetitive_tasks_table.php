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
        Schema::disableForeignKeyConstraints();

        Schema::create('repetitive_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('task_id')->references('id')->on('tasks');
            $table->bigInteger('project_id')->references('id')->on('projects');
            $table->bigInteger('created_by')->references('id')->on('users');
            $table->enum('repetition_rate', ["daily","weekly","monthly","yearly"]);
            $table->dateTime('recurrence_interval');
            $table->bigInteger('recurrence_days')->nullable();
            $table->integer('recurrence_month_day')->nullable();
            $table->integer('recurrence_month')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->dateTime('next_occurrence');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repetitive_tasks');
    }
};
