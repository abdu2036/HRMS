<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
// 🚀 استدعاء الكلاسات المسؤولة عن أخطاء الصلاحيات
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
   public function register(): void
{
    $this->reportable(function (Throwable $e) {
        //
    });

    // 🔥 تعديل شامل: التقاط أي خطأ يحمل رمز 403 أو عدم صلاحية
    $this->renderable(function (\Throwable $e, $request) {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException || 
            $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
            get_class($e) === 'Spatie\Permission\Exceptions\UnauthorizedException' ||
            (method_exists($e, 'getStatusCode') && $e->getStatusCode() == 403)) {
            
            if (!$request->expectsJson()) {
                return redirect()->route('attendances.index')
                    ->with('error', '⚠️ عذراً، لا تمتلك الصلاحيات الكافية للوصول إلى هذه الصفحة.');
            }
        }
    });
}
}