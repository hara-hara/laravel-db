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
            $table->time('real_work_start')->nullable(true);
            $table->time('real_work_end')->nullable(true);
            $table->time('result_work_start')->nullable(true);
            $table->time('result_work_end')->nullable(true);
            $table->string('work_type')->nullable(true);
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('accept')->nullable(true);
            $table->boolean('accAb_id')->nullable(true);
            $table->string('reason')->nullable(true);
            $table->timestamps();

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
