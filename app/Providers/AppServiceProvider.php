<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
/*
use App\Models\Worktime;                // ⭐️ 追加
use Illuminate\Support\Facades\Schema;  // ⭐️ 追加
*/
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        // ⭐️ 追加
        if (Schema::hasTable('worktimes')) {
            $comm_worktimes = Worktime::all();
            view()->share('comm_worktimes', $comm_worktimes);
        }
        */
    }
}
