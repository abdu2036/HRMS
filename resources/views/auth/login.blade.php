c<!DOCTYPE html>
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
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px; /* مسافة بسيطة حول الكارت في الموبايل */
    }

    /* الكارت الرئيسي */
    .login-card-web {
        background: white;
        width: 100%;
        max-width: 850px;
        min-height: 500px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: row; /* للكمبيوتر */
        overflow: hidden;
    }

    /* الجانب البصري (الأزرق) */
    .login-visual {
        flex: 1.2;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        text-align: center;
    }

    .visual-img {
        width: 140px !important;
        height: 140px !important;
        object-fit: contain;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.2);
        background-color: white;
        padding: 10px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .login-visual h2 { font-weight: 700; font-size: 1.6rem; margin-bottom: 10px; }
    .login-visual p { font-size: 0.95rem; opacity: 0.9; line-height: 1.6; }

    /* جانب الفورم */
    .login-form-area {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-area-header h3 { font-weight: 700; font-size: 1.4rem; text-align: center; }
    .form-area-header p { text-align: center; margin-bottom: 25px; }

    /* الحقول */
    .input-group-modern { position: relative; margin-bottom: 15px; }
    .input-group-modern .form-control {
        border-radius: 25px;
        padding: 12px 15px 12px 45px;
        border: 1px solid #e0e0e0;
        width: 100%;
        font-size: 0.95rem;
    }

    .input-group-modern .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }

    .btn-login-modern {
        background-color: #4e73df;
        border: none;
        color: white;
        font-weight: 700;
        padding: 12px;
        border-radius: 25px;
        width: 100%;
        margin-top: 15px;
        transition: 0.3s;
    }

    /* --- تعديلات الهاتف الذكي --- */
    @media (max-width: 768px) {
        body { padding: 0; align-items: flex-start; } /* يبدأ من الأعلى في الموبايل */
        
        .login-card-web {
            flex-direction: column; /* العناصر تحت بعض */
            border-radius: 0; /* ملء الشاشة في الموبايل */
            box-shadow: none;
            min-height: 100vh;
        }

        .login-visual {
            padding: 40px 20px;
            flex: none; /* لا يتمدد ليأخذ مساحة أكبر من اللازم */
        }

        .visual-img {
            width: 100px !important;
            height: 100px !important;
        }

        .login-visual h2 { font-size: 1.3rem; }

        .login-form-area {
            padding: 30px 25px;
            flex: 1;
        }

        .page-footer {
            position: relative;
            bottom: 0;
            padding: 20px 10px;
            background: #fff;
        }
    }
</style>
</head>
<body>

    <div class="login-card-web">
        <div class="login-visual no-print">
            <img src="{{ asset('assets/admin/dist/img/2027.png') }}" alt="شعار الشركة" class="visual-img">
            
            <h2>أهلاً بعودتك!</h2>
            <p>نظام HRMSالموحد المطور خصيصاً لموظفي وإدارة شركة "الحدائق 
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