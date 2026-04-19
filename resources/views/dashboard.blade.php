@extends('layouts.admin')

@section('title')
الرئيسية
@endsection

@section('content')
{{-- تمت إزالة الـ content-wrapper الخارجي لتجنب الهوامش المفرطة --}}
<div class="p-3"> {{-- إضافة padding بسيط وموحد فقط --}}
    
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-12 text-center text-md-right"> {{-- ضبط محاذاة النص --}}
                    <h1 class="m-0 text-dark font-weight-bold">
                        لوحة التحكم <small class="text-muted d-block d-md-inline" style="font-size: 16px;">| نظام إدارة الموارد البشرية</small>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 mb-3">
                    <div class="small-box bg-info shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div class="inner">
                            <h3>{{ \App\Models\Employee::count() }}</h3>
                            <p>إجمالي الموظفين</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('employees.index') }}" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 mb-3">
                    <div class="small-box bg-success shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div class="inner">
                            <h3>{{ \App\Models\Department::count() }}</h3>
                            <p>الأقسام والإدارات</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <a href="{{ route('departments.index') }}" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 mb-3">
                    <div class="small-box bg-warning shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div class="inner">
                            <h3 class="text-white">{{ \App\Models\User::count() }}</h3>
                            <p class="text-white">مستخدمي النظام</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <a href="{{ route('users.index') }}" class="small-box-footer" style="color:rgba(255,255,255,0.8) !important;">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 mb-3">
                    <div class="small-box bg-danger shadow-sm h-100 d-flex flex-column justify-content-between">
                        <div class="inner">
                            <h3>{{ \Spatie\Permission\Models\Role::count() }}</h3>
                            <p>الأدوار والصلاحيات</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <a href="{{ route('roles.index') }}" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-10 offset-md-1 col-12"> {{-- جعل البطاقة مركزية --}}
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-body text-center p-5">
                            {{-- استخدام صورة HR المناسبة إذا كانت متوفرة --}}
                            @if(file_exists(public_path('assets/img/welcome_hr.svg')))
                                <img src="{{ asset('assets/img/welcome_hr.svg') }}" alt="HR" style="max-width: 250px;" class="mb-4">
                            @endif
                            <h2 class="text-primary font-weight-bold">مرحباً بك، {{ auth()->user()->name }}</h2>
                            <p class="lead text-muted">أنت الآن تتصفح نظام HRMS لإدارة الموارد البشرية. يمكنك البدء بإدارة الموظفين أو مراجعة الهيكل التنظيمي من القائمة الجانبية.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection