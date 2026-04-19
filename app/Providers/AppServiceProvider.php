<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
    // مشاركة المراسلات غير المقروءة مع الناف بار فقط
    view()->composer('layouts.admin', function ($view) {
        if (auth()->check()) {
            $user = auth()->user();
            $deptId = optional($user->employee)->department_id;

            $unreadMails = \App\Models\Correspondence::with('sender')
                ->where(function($query) use ($user, $deptId) {
                    $query->where('receiver_id', $user->id);
                    if ($deptId) {
                        $query->orWhere('receiver_department_id', $deptId);
                    }
                })
                ->whereNull('read_at') // الرسائل التي لم تقرأ بعد
                ->latest()
                ->take(5) // عرض آخر 5 فقط
                ->get();

            $view->with('unreadMails', $unreadMails);
        }
    });
}
}
