@extends('layouts.admin')
@section('title', 'إضافة موظف جديد')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark"><i class="fas fa-user-plus mr-2"></i> إضافة موظف جديد</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mx-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between text-center">
                        <div class="step-item active" id="step-1-indicator">
                            <span class="step-number bg-primary text-white p-2 rounded-circle shadow-sm"><i
                                    class="fas fa-user-circle"></i></span>
                            <p class="mt-2 font-weight-bold">البيانات الأساسية</p>
                        </div>
                        <div class="step-item" id="step-2-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i
                                    class="fas fa-id-card-alt text-success"></i></span>
                            <p class="mt-2">البيانات الإدارية</p>
                        </div>
                        <div class="step-item" id="step-3-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i
                                    class="fas fa-wallet text-warning"></i></span>
                            <p class="mt-2">الماليات والملاحظات</p>
                        </div>
                        <div class="step-item" id="step-4-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i
                                    class="fas fa-paperclip text-navy"></i></span>
                            <p class="mt-2">المرفقات</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                @csrf

                <div class="step-content shadow" id="step-1">
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title text-primary font-weight-bold">
                <i class="fas fa-user-circle mr-2"></i> 1. البيانات الشخصية
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">الاسم الكامل <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                               value="{{ old('full_name') }}" required placeholder="أدخل الاسم الرباعي">
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">الجنس <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        </div>
                        <select name="gender" class="form-control" required>
                            <option value="" selected disabled>اختر...</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">تاريخ الميلاد <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth') }}" required>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">الرقم الوطني / الإقامة <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        </div>
                        <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror" 
                               value="{{ old('national_id') }}" required placeholder="1234567890">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">تاريخ انتهاء الهوية <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                        </div>
                        <input type="date" name="id_expiry_date" class="form-control @error('id_expiry_date') is-invalid @enderror" 
                               value="{{ old('id_expiry_date') }}" required>
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">الحالة الاجتماعية <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-heart"></i></span>
                        </div>
                        <select name="marital_status" class="form-control" required>
                            <option value="" selected disabled>اختر الحالة...</option>
                            <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>أعزب</option>
                            <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>متزوج</option>
                            <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>مطلق</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">المؤهل العلمي <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                        </div>
                        <input type="text" name="qualification" class="form-control" 
                               value="{{ old('qualification') }}" placeholder="مثلاً: بكالوريوس هندسة" required>
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">البريد الإلكتروني</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" placeholder="example@mail.com">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">رقم الهاتف <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone') }}" placeholder="09XXXXXXXX" required>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="font-weight-bold">العنوان الكامل <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                        </div>
                        <input type="text" name="address" class="form-control" 
                               value="{{ old('address') }}" required placeholder="المدينة، المنطقة، أقرب معلم">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                {{-- الخطوة 2: البيانات الإدارية --}}
                <div class="step-content d-none shadow-sm" id="step-2">
    <div class="card card-success card-outline shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title text-success font-weight-bold">
                <i class="fas fa-id-card-alt mr-2"></i> 2. البيانات الإدارية والوظيفية
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">كود الموظف <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                        </div>
                        <input type="text" name="employee_code"
                               class="form-control bg-light font-weight-bold text-primary text-center"
                               value="{{ $generatedCode }}" readonly title="يتم توليد الكود تلقائياً">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">كود البصمة (جهاز البصمة)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                        </div>
                        <input type="text" name="fingerprint_code" class="form-control"
                               value="{{ old('fingerprint_code') }}" placeholder="رقم الموظف في الجهاز">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">حالة الموظف <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                        </div>
                        <select name="status" class="form-control select2bs4">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">تاريخ التعيين <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                        </div>
                        <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}" required>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">نوع التوظيف <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        </div>
                        <select name="employment_type" class="form-control" required>
                            <option value="" selected disabled>اختر النوع...</option>
                            <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                            <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                            <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>عقد مؤقت</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">فرع العمل <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                        </div>
                        <select name="branch_id" class="form-control select2" required style="width: 80%;">
                            <option value="" selected disabled>اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">القسم <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-sitemap"></i></span>
                        </div>
                        <select name="department_id" class="form-control select2" required style="width: 80%;">
                            <option value="" selected disabled>اختر القسم</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">المسمى الوظيفي <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        </div>
                        <select name="job_title_id" class="form-control select2" required style="width: 80%;">
                            <option value="" selected disabled>اختر الوظيفة</option>
                            @foreach($jobTitles as $title)
                                <option value="{{ $title->id }}" {{ old('job_title_id') == $title->id ? 'selected' : '' }}>{{ $title->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">الوردية (الشفت) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                        </div>
                        <select name="shift_id" class="form-control" required>
                            <option value="" selected disabled>اختر الشفت</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                <div class="step-content d-none shadow" id="step-3">
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title text-warning">
                <i class="fas fa-wallet mr-2"></i> 3. الماليات والملاحظات
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">الراتب الأساسي <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-money-bill-wave text-success"></i></span>
                        </div>
                        <input type="number" step="0.01" name="basic_salary" class="form-control"
                            value="{{ old('basic_salary') }}" placeholder="0.00" required>
                        <div class="input-group-append">
                            <span class="input-group-text font-weight-bold">LYD</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">رقم الحساب (IBAN)</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-university text-info"></i></span>
                        </div>
                        <input type="text" name="iban" class="form-control" placeholder="LY0000..."
                            value="{{ old('iban') }}">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">رصيد الإجازات السنوية</label>
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white text-primary"><i class="fas fa-calendar-check"></i></span>
                        </div>
                        <input type="number" name="total_leave_balance" class="form-control" 
                               id="total_leave_balance" value="{{ old('total_leave_balance', 0) }}" min="0">
                        <div class="input-group-append">
                            <span class="input-group-text">يوم</span>
                        </div>
                    </div>
                    <small class="text-muted">اتركه 0 إذا لم يوجد رصيد حالي.</small>
                </div>

                <div class="col-md-6 form-group">
                    <div class="p-2 border rounded bg-light shadow-sm" style="border-left: 5px solid #ffc107 !important;">
                        <label class="font-weight-bold text-navy mb-1">
                            <i class="fas fa-ticket-alt mr-1 text-warning"></i> رصيد التذاكر الشهري المجاني
                        </label>
                        <div class="input-group">
                            <input type="number" name="monthly_tickets_limit" 
                                   class="form-control text-center font-weight-bold bg-white" 
                                   value="{{ old('monthly_tickets_limit', 48) }}" readonly>
                        </div>
                        <small class="text-muted d-block mt-1">يُمنح تلقائياً عند بداية كل شهر.</small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="form-group">
                <label class="font-weight-bold"><i class="fas fa-sticky-note mr-1 text-secondary"></i> ملاحظات إضافية</label>
                <textarea name="notes" class="form-control shadow-sm" rows="3"
                    placeholder="أي معلومات مالية أو ملاحظات تخص الموظف...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
</div>

               <div class="step-content d-none shadow" id="step-4">
    <div class="card card-navy card-outline">
        <div class="card-header bg-navy text-white">
            <h3 class="card-title text-white">
                <i class="fas fa-paperclip mr-2 text-white"></i> 4. المرفقات والوثائق الصحية
            </h3>
        </div>
        <div class="card-body">
            {{-- الصف الأول: المرفقات الأساسية --}}
            <div class="row mb-4">
                <div class="col-md-6 border-right">
                    <div class="form-group border rounded p-4 text-center bg-light shadow-sm" style="min-height: 180px;">
                        <label class="d-block font-weight-bold mb-3">
                            <i class="fas fa-user-circle text-primary"></i> صورة الموظف الشخصية 
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="profile_photo" class="form-control-file d-inline-block" required>
                        <small class="text-muted d-block mt-2">يرجى رفع صورة خلفية بيضاء</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group border rounded p-4 text-center bg-light shadow-sm" style="min-height: 180px;">
                        <label class="d-block font-weight-bold mb-3">
                            <i class="fas fa-id-card text-primary"></i> إثبات الهوية (PDF/Image) 
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="id_proof" class="form-control-file d-inline-block" required>
                        <small class="text-muted d-block mt-2">جواز سفر، رقم وطني، أو إقامة</small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- الصف الثاني: الشهادة الصحية (الإضافة الجديدة المنسقة) --}}
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="form-group border rounded p-4 text-center bg-light shadow-sm" style="min-height: 180px; display: flex; align-items: center; flex-direction: column; justify-content: center;">
                        <label class="d-block font-weight-bold mb-3 text-danger">
                            <i class="fas fa-calendar-times"></i> تاريخ انتهاء الشهادة الصحية
                        </label>
                        <input type="date" name="health_certificate_expiry" class="form-control" style="max-width: 250px;">
                        <small class="text-muted d-block mt-2">سيقوم النظام بتنبيهك قبل الانتهاء</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group border rounded p-4 text-center bg-light shadow-sm" style="min-height: 180px; display: flex; align-items: center; flex-direction: column; justify-content: center;">
                        <label class="d-block font-weight-bold mb-3 text-success">
                            <i class="fas fa-file-medical"></i> مرفق الشهادة الصحية
                        </label>
                        <input type="file" name="health_certificate_file" class="form-control-file d-inline-block">
                        <small class="text-muted d-block mt-2">يرجى رفع نسخة واضحة (Image/PDF)</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

                <div class="card-footer bg-white text-right border-top mt-3">
                    <button type="button" class="btn btn-secondary px-4 d-none shadow-sm" id="prevBtn"
                        onclick="nextPrev(-1, 'employeeForm', 4)">
                        <i class="fas fa-arrow-right ml-1"></i> السابق
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="nextBtn"
                        onclick="nextPrev(1, 'employeeForm', 4)">
                        التالي <i class="fas fa-arrow-left mr-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        // كود JavaScript لإبقاء المستخدم في الخطوة الصحيحة عند وجود أخطاء
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'تنبيه',
                text: 'يرجى تصحيح الأخطاء في الحقول الموضحة قبل الحفظ',
                confirmButtonText: 'حسناً'
            });
        @endif
    </script>
@endsection