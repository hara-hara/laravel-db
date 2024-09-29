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
        Schema::create('master_worktime_types', function (Blueprint $table) {
            $table->id();
            $table->time('able_worktime_start');
            $table->time('able_worktime_end');
            $table->time('basic_worktime_start');
            $table->time('basic_worktime_end');
            $table->integer('lunch_break_times');
            $table->integer('dayoff_times');
            $table->integer('morningoff_times');
            $table->integer('aftenoonoff_times');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_worktime_types');
    }
};
