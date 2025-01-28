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
        Schema::table('users', function (Blueprint $table) {
            $table->string('member_no')->comment('従業員No.')->after('remember_token'); 
            $table->date('join_date')->nullable()->comment('入所年月日')->after('member_no'); //nullable()が無いとエラーになる    
            $table->integer('cur_used_dayoff')->comment('有休取得日数')->after('join_date'); 
            $table->integer('cur_getting_dayoff')->comment('有休残日数')->after('cur_used_dayoff'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('member_no');
            $table->dropColumn('join_date');
            $table->dropColumn('cur_used_dayoff');
            $table->dropColumn('cur_getting_dayoff');
        });
    }
};
