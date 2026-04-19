<?php

namespace App\Http\Middleware; // تأكد أن هذا السطر مكتوب في بداية الملف

use Closure;
use Illuminate\Http\Request;

class CheckIfEmployee
{
public function handle($request, Closure $next)
{
    // التأكد من تسجيل الدخول
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    // التحقق من وجود علاقة مع موديل Employee
    // تأكد أنك قمت بتعريف علاقة employee() في موديل User
    if ($user->employee) {
        return $next($request);
    }

    // رسالة الخطأ التي تظهر لك في الصورة تأتي من هنا
    return redirect('/dashboard')->with('error', 'هذه الصفحة مخصصة للموظفين فقط.');
}
}
