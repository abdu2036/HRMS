<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول | نظام HRMS لإدارة الموارد البشرية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: #f0f2f5; 
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* الكارت الرئيسي (Container) */
        .login-card-web {
            background: white;
            width: 850px;
            height: 500px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            display: flex;
            overflow: hidden; /* لضمان عدم خروج المحتوى عن الحواف المنحنية */
        }

        /* الجانب البصري (الأيسر) */
        .login-visual {
            flex: 1.2;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }
.visual-img {
    width: 180px !important; /* حجم متناسق للشعار */
    height: 180px !important;
    object-fit: cover; /* لضمان عدم تمدد الصورة */
    border-radius: 50%; /* جعل الصورة دائرية تماماً */
    border: 5px solid rgba(255, 255, 255, 0.2); /* إطار شفاف عصري */
    background-color: white; /* خلفية بيضاء خلف الشعار ليبرز */
    padding: 5px;
    margin-bottom: 25px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* ظل ناعم لإعطاء عمق */
    display: block;
    margin-left: auto;
    margin-right: auto;
}

/* تنسيق النصوص بجانب الشعار */
.login-visual h2 {
    font-weight: 800;
    margin-top: 10px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.login-visual p {
    font-size: 1rem;
    opacity: 0.85;
    max-width: 300px;
    margin: 0 auto;
}

        /* الجانب الخاص بالبيانات (الأيمن) */
        .login-form-area {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-area-header { text-align: center; margin-bottom: 30px; }
        .form-area-header h3 { font-weight: 700; color: #1a1a1a; margin-bottom: 5px; }
        .form-area-header p { text-muted; }

        /* تنسيق الحقول */
        .input-group-modern {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-modern .form-control {
            border-radius: 30px;
            padding: 12px 20px 12px 45px; /* مساحة للأيقونة جهة اليسار */
            border: 1px solid #ddd;
            height: auto;
            font-size: 1rem;
        }

        .input-group-modern .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            font-size: 1.1rem;
            z-index: 10;
        }

        /* زر الدخول */
        .btn-login-modern {
            background-color: #4e73df;
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 30px;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .btn-login-modern:hover { background-color: #2e59d9; }

        /* الروابط الإضافية */
        .extra-links { text-align: center; margin-top: 20px; font-size: 0.95rem; }
        .extra-links a { color: #4e73df; text-decoration: none; font-weight: 600; }
        .extra-links a:hover { text-decoration: underline; }

        /* تذييل الصفحة */
        .page-footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="login-card-web">
        <div class="login-visual no-print">
            <img src="https://scontent.fmji4-2.fna.fbcdn.net/v/t39.30808-6/535458773_1182094077288007_6356981769473909481_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&_nc_ohc=fW7Ggfn7XsgQ7kNvwGs1af4&_nc_oc=AdpDbJr-M9SdZaisgRJvj8U3Q-CsL09as15icWAXgRpXen1__h0pFn8pcQX_bfz5ERM&_nc_zt=23&_nc_ht=scontent.fmji4-2.fna&_nc_gid=vOkolxhGt0_J7YunoD6IFw&_nc_ss=7a3a8&oh=00_Af3c31h1xwnPA8awyh6Vi5gYEq1AU6wp1QXh-VA6o0ISrw&oe=69D959FF" alt="HR Visual" class="visual-img shadow">
            
            <h2>أهلاً بعودتك!</h2>
            <p>نظام HRMS الموحد المطور خصيصاً لموظفي وإدارة شركة "دار المرح
            "</p>
        </div>
        
        <div class="login-form-area">
            <div class="form-area-header">
                <h3>نظام HRMS</h3>
                <p class="text-muted">مرحباً بك، سجل دخولك للبدء</p>
            </div>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="input-group-modern">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                
                <div class="input-group-modern">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="كلمة المرور" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                
                <div class="row align-items-center mt-3">
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-normal text-muted" for="remember">تذكرني</label>
                        </div>
                    </div>
                    <div class="col-6 text-left">
                        <a href="{{ route('password.request') }}" class="text-muted small">نسيت كلمة المرور؟</a>
                    </div>
                </div>
                
                <button type="submit" class="btn-login-modern shadow">
                    تسجيل الدخول <i class="fas fa-arrow-left mr-1"></i>
                </button>
            </form>
            
           <!-- <div class="extra-links border-top pt-3">
                <p class="text-muted">ليس لديك حساب موظف؟ <a href="{{ route('register') }}">تسجيل موظف جديد</a></p>
            </div>
        </div>
    </div> -->

    <div class="page-footer">
        جميع الحقوق محفوظة © {{ date('Y') }} | نظام إدارة الموارد البشرية HRMS
    </div>
    </div>

</body>
</html>