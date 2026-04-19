<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>استعادة كلمة المرور | HRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        /* نستخدم نفس التنسيقات التي في صفحة Login */
        body { font-family: 'Cairo', sans-serif; background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card-web { background: white; width: 850px; height: 500px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); display: flex; overflow: hidden; }
        .login-visual { flex: 1.2; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; }
        .visual-img { width: 150px !important; height: 150px !important; object-fit: cover; border-radius: 50%; border: 5px solid rgba(255, 255, 255, 0.2); background-color: white; padding: 5px; margin-bottom: 25px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); }
        .login-form-area { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .input-group-modern { position: relative; margin-bottom: 20px; }
        .input-group-modern .form-control { border-radius: 30px; padding: 12px 20px 12px 45px; border: 1px solid #ddd; height: auto; }
        .input-group-modern .input-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #ccc; z-index: 10; }
        .btn-login-modern { background-color: #4e73df; border: none; color: white; font-weight: 700; padding: 12px; border-radius: 30px; width: 100%; transition: 0.3s; }
        .btn-login-modern:hover { background-color: #2e59d9; }
        .extra-links { text-align: center; margin-top: 20px; }
        .extra-links a { color: #4e73df; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-card-web">
        <div class="login-visual no-print">
            <img src="https://scontent.fmji4-2.fna.fbcdn.net/v/t39.30808-6/535458773_1182094077288007_6356981769473909481_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&oh=00_Af3c31h1xwnPA8awyh6Vi5gYEq1AU6wp1QXh-VA6o0ISrw&oe=69D959FF" alt="Logo" class="visual-img shadow">
            <h2>هل نسيت الكلمة؟</h2>
            <p>لا تقلق، أدخل بريدك وسنرسل لك رابطاً لتعيين كلمة مرور جديدة بكل سهولة.</p>
        </div>
        <div class="login-form-area">
            <div class="form-area-header text-center mb-4">
                <h3>استعادة الوصول</h3>
                <p class="text-muted small">أدخل بريدك الإلكتروني المسجل</p>
            </div>
            @if (session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-group-modern">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required>
                </div>
                <button type="submit" class="btn-login-modern shadow">إرسال الرابط <i class="fas fa-paper-plane mr-1"></i></button>
            </form>
            <div class="extra-links border-top pt-3">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-right ml-1"></i> العودة للدخول</a>
            </div>
        </div>
    </div>
</body>
</html>