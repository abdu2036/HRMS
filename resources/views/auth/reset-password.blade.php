<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تغيير كلمة المرور | HRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style> /* نفس استايل Login */ </style>
</head>
<body>
    <div class="login-card-web">
        <div class="login-visual no-print">
            <img src="https://scontent.fmji4-2.fna.fbcdn.net/v/t39.30808-6/535458773_1182094077288007_6356981769473909481_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&oh=00_Af3c31h1xwnPA8awyh6Vi5gYEq1AU6wp1QXh-VA6o0ISrw&oe=69D959FF" alt="Logo" class="visual-img">
            <h2>خطوة واحدة تبقت!</h2>
            <p>أدخل كلمة المرور الجديدة الخاصة بك الآن لتتمكن من العودة للنظام.</p>
        </div>
        <div class="login-form-area">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="input-group-modern">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" value="{{ $request->email }}" readonly>
                </div>
                <div class="input-group-modern">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="كلمة المرور الجديدة" required autofocus>
                </div>
                <div class="input-group-modern">
                    <i class="fas fa-check-double input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد الكلمة" required>
                </div>
                <button type="submit" class="btn-login-modern shadow">تحديث الكلمة <i class="fas fa-key mr-1"></i></button>
            </form>
        </div>
    </div>
</body>
</html>