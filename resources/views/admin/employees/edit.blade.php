@extends('layouts.admin')

{{-- عنوان الصفحة في المتصفح --}}
@section('title', 'تعديل بيانات موظف')

{{-- المحتوى الرئيسي --}}
@section('content')
    <div class="content-header">
        <div class="container-fluid">
         
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
            {{-- مؤشر الخطوات (Indicators) --}}
            <div class="card card-default shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between text-center">
                        <div class="step-item active" id="step-1-indicator">
                            <span class="step-number bg-primary text-white p-2 rounded-circle shadow-sm"><i class="fas fa-user-circle"></i></span>
                            <p class="mt-2 font-weight-bold">البيانات الأساسية</p>
                        </div>
                        <div class="step-item" id="step-2-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i class="fas fa-id-card-alt text-success"></i></span>
                            <p class="mt-2">البيانات الإدارية</p>
                        </div>
                        <div class="step-item" id="step-3-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i class="fas fa-wallet text-warning"></i></span>
                            <p class="mt-2">الماليات والملاحظات</p>
                        </div>
                        <div class="step-item" id="step-4-indicator">
                            <span class="step-number bg-light p-2 rounded-circle border"><i class="fas fa-paperclip text-navy"></i></span>
                            <p class="mt-2">المرفقات</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" id="editEmployeeForm">
                @csrf
                @method('PUT')

                {{-- الخطوة 1: البيانات الشخصية --}}
                <div class="step-content shadow" id="step-1">
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title text-primary font-weight-bold">
                <i class="fas fa-user-circle mr-2"></i> 1. البيانات الشخصية الأساسية
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
                               value="{{ old('full_name', $employee->full_name) }}" required placeholder="أدخل الاسم الرباعي">
                    </div>
                </div>

                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">الجنس <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        </div>
                        <select name="gender" class="form-control" required>
                            <option value="male" {{ (old('gender', $employee->gender) == 'male') ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ (old('gender', $employee->gender) == 'female') ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 form-group">
                    <label class="font-weight-bold">تاريخ الميلاد <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                        </div>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth', $employee->date_of_birth) }}" required>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">الرقم الوطني / الإقامة <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        </div>
                        <input type="text" name="national_id" class="form-control" 
                               value="{{ old('national_id', $employee->national_id) }}" required placeholder="1234567890">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">تاريخ انتهاء الهوية <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                        </div>
                        <input type="date" name="id_expiry_date" class="form-control @error('id_expiry_date') is-invalid @enderror" 
                               value="{{ old('id_expiry_date', $employee->id_expiry_date) }}" required>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">الحالة الاجتماعية <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                        </div>
                        <select name="marital_status" class="form-control" required>
                            <option value="" selected disabled>اختر الحالة الاجتماعية</option>
                            <option value="single" {{ (old('marital_status', $employee->marital_status) == 'single') ? 'selected' : '' }}>أعزب</option>
                            <option value="married" {{ (old('marital_status', $employee->marital_status) == 'married') ? 'selected' : '' }}>متزوج</option>
                            <option value="divorced" {{ (old('marital_status', $employee->marital_status) == 'divorced') ? 'selected' : '' }}>مطلق</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">المؤهل العلمي <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                        </div>
                        <input type="text" name="qualification" class="form-control" 
                               value="{{ old('qualification', $employee->qualification) }}" required placeholder="مثلاً: بكالوريوس تقنية معلومات">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">رقم الهاتف <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ old('phone', $employee->phone) }}" required placeholder="09XXXXXXXX">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">البريد الإلكتروني</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $employee->email) }}" placeholder="example@mail.com">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="font-weight-bold">العنوان السكني <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" name="address" class="form-control" 
                               value="{{ old('address', $employee->address) }}" required placeholder="المدينة، الحي، الشارع">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

               {{-- الخطوة 2: البيانات الإدارية --}}
<div class="step-content d-none shadow-sm" id="step-2">
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-id-card-alt mr-2 text-success"></i> 2. البيانات الإدارية</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>كود الموظف (لا يمكن تعديله)</label>
                    <input type="text" name="employee_code" value="{{ $employee->employee_code }}" readonly class="form-control bg-light">
                </div>
                <div class="col-md-4 form-group">
                    <label>كود البصمة</label>
                    <input type="text" name="fingerprint_code" class="form-control" value="{{ old('fingerprint_code', $employee->fingerprint_code) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>حالة الموظف <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-4 form-group">
                    <label>تاريخ التعيين <span class="text-danger">*</span></label>
                    <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date) }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label>نوع التوظيف <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-control" required>
                        <option value="full_time" {{ $employee->employment_type == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                        <option value="part_time" {{ $employee->employment_type == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                        <option value="contract" {{ $employee->employment_type == 'contract' ? 'selected' : '' }}>عقد مؤقت</option>
                    </select>
                </div>
                {{-- إضافة حقل فرع العمل هنا --}}
                <div class="col-md-4 form-group">
                    <label>فرع العمل (الموقع الجغرافي) <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-control select2"value="{{ old('branch_id', $employee->branch_id) }} required style="width: 100%;">
                        <option value="" selected disabled>اختر الفرع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 form-group">
                    <label>الادارة <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-control select2" required style="width: 100%;">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>القسم<span class="text-danger">*</span></label>
                    <select name="job_title_id" class="form-control select2" required style="width: 100%;">
                        @foreach($jobTitles as $title)
                            <option value="{{ $title->id }}" {{ $employee->job_title_id == $title->id ? 'selected' : '' }}>{{ $title->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>الشفت (الوردية) <span class="text-danger">*</span></label>
                    <select name="shift_id" class="form-control" required>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ $employee->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

                {{-- الخطوة 3: الماليات --}}
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
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-money-bill-wave text-success"></i></span>
                        </div>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" 
                               value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">LYD</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">رقم الحساب (IBAN)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-university text-info"></i></span>
                        </div>
                        <input type="text" name="iban" class="form-control" 
                               value="{{ old('iban', $employee->iban) }}" placeholder="LY0000...">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">رصيد الإجازات السنوية</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-primary"></i></span>
                            </div>
                            <input type="number" name="total_leave_balance" class="form-control" 
                                   value="{{ old('total_leave_balance', $employee->total_leave_balance) }}" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text">يوم</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group p-2 border rounded bg-light shadow-sm" style="border-left: 5px solid #ffc107 !important;">
                        <label class="font-weight-bold text-navy">
                            <i class="fas fa-ticket-alt mr-1 text-warning"></i> رصيد التذاكر الشهري المجاني
                        </label>
                        <input type="number" name="monthly_tickets_limit" 
                               class="form-control text-center font-weight-bold" 
                               {{-- جلب القيمة من علاقة التذاكر التي أنشأناها --}}
                               value="{{ old('monthly_tickets_limit', $employee->currentMonthAllowance->monthly_limit ?? 48) }}" 
                               readonly>
                        <small class="text-muted d-block mt-1 text-center">يتم تحديث الرصيد تلقائياً كل شهر.</small>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="font-weight-bold"><i class="fas fa-sticky-note mr-1 text-secondary"></i> ملاحظات إضافية</label>
                <textarea name="notes" class="form-control" rows="3" 
                          placeholder="أي ملاحظات إضافية...">{{ old('notes', $employee->notes) }}</textarea>
            </div>
        </div>
    </div>
</div>

                {{-- الخطوة 4: المرفقات الحالية والجديدة --}}
<div class="step-content d-none shadow" id="step-4">
    <div class="card card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-paperclip mr-2 text-navy"></i> 4. المرفقات والوثائق الصحية</h3>
        </div>
        <div class="card-body">
            {{-- الصف الأول: الصورة الشخصية وإثبات الهوية --}}
            <div class="row text-center mb-4">
                <div class="col-md-6 border-right">
                    <label class="d-block font-weight-bold"><i class="fas fa-user-circle text-primary"></i> الصورة الشخصية</label>
                    @if($employee->profile_photo)
                        <img src="{{ asset('storage/'.$employee->profile_photo) }}" class="img-circle mb-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #ddd;">
                    @endif
                    <div class="form-group p-3 bg-light rounded border">
                        <input type="file" name="profile_photo" class="form-control-file d-inline-block">
                        <small class="text-muted d-block mt-2">اتركه فارغاً للحفاظ على الصورة الحالية</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="d-block font-weight-bold"><i class="fas fa-id-card text-primary"></i> إثبات الهوية (الرقم الوطني/الجواز)</label>
                    @if($employee->id_proof)
                        <a href="{{ asset('storage/'.$employee->id_proof) }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                            <i class="fas fa-external-link-alt"></i> عرض الملف الحالي
                        </a>
                    @else
                        <div class="mb-3 text-muted small">لا يوجد ملف مرفق حالياً</div>
                    @endif
                    <div class="form-group p-3 bg-light rounded border">
                        <input type="file" name="id_proof" class="form-control-file d-inline-block">
                        <small class="text-muted d-block mt-2">يمكنك رفع ملف PDF أو صورة</small>
                    </div>
                </div>
            </div>

            <hr>

            {{-- الصف الثاني: الشهادة الصحية (التنسيق الجديد) --}}
            <div class="row text-center mt-4">
                <div class="col-md-6 border-right">
                    <label class="d-block font-weight-bold text-danger"><i class="fas fa-calendar-times"></i> تاريخ انتهاء الشهادة الصحية</label>
                    <div class="form-group p-3 bg-light rounded border" style="min-height: 110px; display: flex; align-items: center; flex-direction: column; justify-content: center;">
                        <input type="date" name="health_certificate_expiry" class="form-control" value="{{ $employee->health_certificate_expiry }}" style="max-width: 250px;">
                        <small class="text-muted d-block mt-2">سيقوم النظام بتنبيهك قبل الانتهاء</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="d-block font-weight-bold text-success"><i class="fas fa-file-medical"></i> مرفق الشهادة الصحية</label>
                    @if($employee->health_certificate_file)
                        <a href="{{ asset('storage/'.$employee->health_certificate_file) }}" target="_blank" class="btn btn-outline-success btn-sm mb-3">
                            <i class="fas fa-eye"></i> عرض الشهادة الحالية
                        </a>
                    @else
                        <div class="mb-3 text-muted small">لم يتم رفع شهادة بعد</div>
                    @endif
                    <div class="form-group p-3 bg-light rounded border">
                        <input type="file" name="health_certificate_file" class="form-control-file d-inline-block">
                        <small class="text-muted d-block mt-2">يرجى رفع نسخة واضحة (Image/PDF)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                {{-- أزرار التحكم في الخطوات --}}
                <div class="card-footer bg-white text-right border-top mt-3">
                    <button type="button" class="btn btn-secondary px-4 d-none shadow-sm" id="prevBtn" onclick="nextPrev(-1, 'editEmployeeForm', 4)">
                        <i class="fas fa-arrow-right ml-1"></i> السابق
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="nextBtn" onclick="nextPrev(1, 'editEmployeeForm', 4)">
                        التالي <i class="fas fa-arrow-left mr-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
<script>
$(document).ready(function() {
    // التحقق من وجود أخطاء عند تحميل الصفحة
    if ($('.is-invalid').length > 0) {
        // العثور على أول حقل به خطأ وتحديد الـ "Step" الأب له
        var firstErrorStep = $('.is-invalid').closest('.step-content').attr('id');
        
        // إخفاء جميع الخطوات وإظهار الخطوة التي بها الخطأ
        $('.step-content').addClass('d-none');
        $('#' + firstErrorStep).removeClass('d-none');
        
        // تحديث حالة الأزرار (إذا كان لديك نظام أزرار علوي)
        $('.nav-link').removeClass('active');
        $('[data-target="#' + firstErrorStep + '"]').addClass('active');
    }
});
</script>