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
        Schema::create('worktimes', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->date('work_date');
            $table->time('work_start');
            $table->time('work_end');
            $table->string('work_type');
            $table->timestamps();
            //$table->primary(['member_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worktimes');
    }
};
