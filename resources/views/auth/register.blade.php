<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل موظف جديد | نظام HRMS</title>
    
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

        .login-card-web {
            background: white;
            width: 850px;
            min-height: 550px; /* زيادة الارتفاع قليلاً لاستيعاب حقول التسجيل */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            display: flex;
            overflow: hidden;
        }

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
            width: 160px !important;
            height: 160px !important;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.2);
            background-color: white;
            padding: 5px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .login-form-area {
            flex: 1;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-area-header { text-align: center; margin-bottom: 25px; }
        .form-area-header h3 { font-weight: 700; color: #1a1a1a; margin-bottom: 5px; }

        .input-group-modern {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group-modern .form-control {
            border-radius: 30px;
            padding: 10px 20px 10px 45px;
            border: 1px solid #ddd;
            height: auto;
            font-size: 0.95rem;
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            z-index: 10;
        }

        .btn-login-modern {
            background-color: #4e73df;
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 30px;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login-modern:hover { background-color: #2e59d9; }

        .extra-links { text-align: center; margin-top: 20px; font-size: 0.9rem; }
        .extra-links a { color: #4e73df; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <div class="login-card-web">
        <div class="login-visual">
            <img src="https://scontent.fmji4-2.fna.fbcdn.net/v/t39.30808-6/535458773_1182094077288007_6356981769473909481_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&_nc_ohc=fW7Ggfn7XsgQ7kNvwGs1af4&_nc_oc=AdpDbJr-M9SdZaisgRJvj8U3Q-CsL09as15icWAXgRpXen1__h0pFn8pcQX_bfz5ERM&_nc_zt=23&_nc_ht=scontent.fmji4-2.fna&_nc_gid=vOkolxhGt0_J7YunoD6IFw&_nc_ss=7a3a8&oh=00_Af3c31h1xwnPA8awyh6Vi5gYEq1AU6wp1QXh-VA6o0ISrw&oe=69D959FF" alt="HR Visual" class="visual-img shadow">
            <h2>انضم إلينا!</h2>
            <p>أنشئ حسابك الآن لتتمكن من إدارة ملفك الوظيفي في "دار المرح"</p>
        </div>
        
        <div class="login-form-area">
            <div class="form-area-header">
                <h3>تسجيل جديد</h3>
                <p class="text-muted small">أدخل بياناتك لإنشاء حساب موظف</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="input-group-modern">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="الاسم الكامل" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                </div>

                <div class="input-group-modern">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required>
                    @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                </div>
                
                <div class="input-group-modern">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="كلمة المرور" required>
                    @error('password') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                </div>

                <div class="input-group-modern">
                    <i class="fas fa-check-double input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور" required>
                </div>
                
                <button type="submit" class="btn-login-modern shadow">
                    إنشاء الحساب <i class="fas fa-user-plus mr-1"></i>
                </button>
            </form>
            
            <div class="extra-links border-top pt-3">
                <p class="text-muted">لديك حساب بالفعل؟ <a href="{{ route('login') }}">سجل دخولك</a></p>
            </div>
        </div>
    </div>

</body>
</html>