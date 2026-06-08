@extends('layouts.admin')

@section('title', 'ملف الموظف: ' . $employee->full_name)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-user-tie mr-2 text-primary"></i> ملف الموظف</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-edit"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('admin.employees.id_card', $employee->id) }}" target="_blank"
                        class="btn btn-secondary">
                        <i class="fas fa-id-card"></i> طباعة بطاقة تعريف
                    </a>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left"></i> عودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- العمود الجانبي: الصورة والمعلومات الأساسية --}}
                <div class="col-md-3">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle shadow border-primary"
                                    src="{{ $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : asset('assets/img/default.png') }}"
                                    alt="User profile picture" style="width: 110px; height: 110px; object-fit: cover;">
                            </div>

                            <h3 class="profile-username text-center mt-3 font-weight-bold">{{ $employee->full_name }}</h3>
                            <p class="text-muted text-center mb-1">{{ $employee->jobTitle->name ?? 'غير محدد' }}</p>
                            <p class="text-center"><span
                                    class="badge badge-light border">{{ $employee->employment_type_text }}</span></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>كود الموظف</b> <span
                                        class="float-right text-primary font-weight-bold">{{ $employee->employee_code }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>القسم</b> <span class="float-right">{{ $employee->department->name ?? '---' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>الفرع</b> <span class="float-right text-info"><i class="fas fa-map-marker-alt"></i>
                                        {{ $employee->branch->name ?? '---' }}</span>
                                </li>
                                <li class="list-group-item border-bottom-0">
                                    <b>الحالة</b>
                                    <span
                                        class="float-right badge {{ ($employee->status == 'active' || $employee->status == 1) ? 'badge-success' : 'badge-danger' }} shadow-sm">
                                        {{ ($employee->status == 'active' || $employee->status == 1) ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>رصيد الإجازات السنوي</b>
                                    <span class="float-right badge badge-info shadow-sm">
                                        <i class="fas fa-plane-departure fa-xs mr-1"></i>
                                        {{ $employee->total_leave_balance ?? 0 }} يوم
                                    </span>
                                </li>
                                {{-- رصيد التذاكر في العمود الجانبي --}}
<!-- <li class="list-group-item bg-light mt-2 border rounded shadow-sm">
    <b><i class="fas fa-ticket-alt text-warning mr-1"></i> رصيد التذاكر (الشهر الحالي)</b>
    <div class="text-center mt-2">
        <h4 class="font-weight-bold text-success mb-0">
            {{ $employee->currentMonthAllowance->current_balance ?? 0 }} 
            <small class="text-muted">/ {{ $employee->currentMonthAllowance->monthly_limit ?? 48 }}</small>
        </h4>
        <button type="button" class="btn btn-sm btn-warning btn-block mt-2 font-weight-bold" data-toggle="modal" data-target="#spendTicketsModal">
            <i class="fas fa-minus-circle"></i> صرف تذاكر
        </button>
    </div>
</li> -->
                            </ul>
                        </div>
                    </div>

                    <div class="card card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">معلومات الاتصال</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-phone-alt mr-1 text-primary"></i> الهاتف</strong>
                            <p class="text-muted">{{ $employee->phone }}</p>
                            <hr>
                            <strong><i class="fas fa-envelope mr-1 text-primary"></i> البريد</strong>
                            <p class="text-muted">{{ $employee->email ?? 'لا يوجد' }}</p>
                            <hr>
                            <strong><i class="fas fa-map-marked mr-1 text-primary"></i> العنوان</strong>
                            <p class="text-muted mb-0">{{ $employee->address }}</p>
                        </div>
                    </div>
                </div>

                {{-- العمود الرئيسي: التبويبات والتفاصيل --}}
                <div class="col-md-9">
                    <div class="card shadow-sm">
                        <div class="card-header p-2 bg-white">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab"><i
                                            class="fas fa-info-circle"></i> التفاصيل الإدارية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#financial" data-toggle="tab"><i
                                            class="fas fa-wallet"></i> البيانات المالية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#attachments" data-toggle="tab"><i
                                            class="fas fa-paperclip"></i> المرفقات</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary border-bottom pb-2 font-weight-bold">بيانات الهوية
                                                والنشأة</h6>
                                            <table class="table table-sm table-borderless bg-light rounded">
                                                <tr>
                                                    <th width="45%">تاريخ الميلاد</th>
                                                    <td>{{ $employee->date_of_birth }}</td>
                                                </tr>
                                                <tr>
                                                    <th>الرقم الوطني</th>
                                                    <td>{{ $employee->national_id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>تاريخ انتهاء الهوية</th>
                                                    <td class="text-danger font-weight-bold">
                                                        {{ $employee->id_expiry_date ?? '---' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>الحالة الاجتماعية</th>
                                                    <td>{{ $employee->marital_status }}</td>
                                                </tr>
                                                <tr>
                                                    <th>الجنسية</th>
                                                    <td class="text-success font-weight-bold">
                                                        {{ $employee->qualification ?? '---' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>تاريخ انتهاء الشهادة الصحية</th>
                                                    <td class="text-danger font-weight-bold">
                                                        {{ $employee->health_certificate_expiry ?? '---' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary border-bottom pb-2 font-weight-bold">بيانات العمل</h6>
                                            <table class="table table-sm table-borderless bg-light rounded">
                                                <tr>
                                                    <th width="45%">تاريخ التعيين</th>
                                                    <td>{{ $employee->hire_date }}</td>
                                                </tr>
                                                <tr>
                                                    <th>كود البصمة</th>
                                                    <td class="text-primary font-weight-bold"><i
                                                            class="fas fa-fingerprint fa-sm"></i>
                                                        {{ $employee->fingerprint_code ?? '---' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>نوع التوظيف</th>
                                                    <td>{{ $employee->employment_type }}</td>
                                                </tr>
                                                <tr>
                                                    <th>الشفت (الوردية)</th>
                                                    <td>{{ $employee->shift->name ?? '---' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h6 class="font-weight-bold"><i class="fas fa-sticky-note text-warning"></i> ملاحظات
                                            إضافية:</h6>
                                        <div class="p-3 border rounded bg-light shadow-none">
                                            {{ $employee->notes ?? 'لا توجد ملاحظات مسجلة لهذا الموظف.' }}
                                        </div>
                                    </div>
                                </div>
<div class="tab-pane" id="financial">
    <div class="row">
        {{-- الراتب --}}
        <div class="col-md-5">
            <div class="info-box bg-white border shadow-none">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-check-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">الراتب الأساسي</span>
                    <span class="info-box-number text-xl text-dark">
                        {{ number_format($employee->basic_salary, 2) }} <small>LYD</small>
                    </span>
                </div>
            </div>
        </div>

        {{-- [الجديد] رصيد التذاكر كصندوق معلومات --}}
        <div class="col-md-5">
            <div class="info-box bg-white border shadow-none">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-ticket-alt text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">رصيد التذاكر المتاح</span>
                    <span class="info-box-number text-xl text-dark">
                        {{ $employee->currentMonthAllowance->current_balance ?? 0 }} تذكرة
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- باقي جدول البيانات المالية (IBAN) --}}
    <table class="table table-striped mt-3 border">
        <tr class="bg-light">
            <th width="30%">رقم الحساب (IBAN)</th>
            <td class="text-monospace font-weight-bold text-primary">
                {{ $employee->iban ?? 'غير مسجل' }}
            </td>
        </tr>
    </table>
</div>

                                <div class="tab-pane" id="attachments">
                                    <div class="row d-flex align-items-stretch"> {{-- d-flex لضمان تساوي ارتفاع البطاقات
                                        --}}

                                        {{-- بطاقة الهوية --}}
                                        @if($employee->id_proof)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border shadow-none text-center p-3 h-100"> {{-- h-100 لتوحيد
                                                    الارتفاع --}}
                                                    <div class="mb-3">
                                                        <i class="fas fa-file-pdf fa-4x text-danger"></i>
                                                    </div>
                                                    <h6 class="font-weight-bold mb-2">صورة الهوية / جواز السفر</h6>
                                                    <div class="mt-auto"> {{-- دفع الأزرار للأسفل دائماً --}}
                                                        <div class="btn-group w-100">
                                                            <a href="{{ asset('storage/' . $employee->id_proof) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye"></i> عرض
                                                            </a>
                                                            <a href="{{ asset('storage/' . $employee->id_proof) }}" download
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-download"></i> تحميل
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- بطاقة الشهادة الصحية --}}
                                        @if($employee->health_certificate_file)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border shadow-none text-center p-3 h-100"> {{-- h-100 لتوحيد
                                                    الارتفاع --}}
                                                    <div class="mb-3">
                                                        <i class="fas fa-file-medical fa-4x text-success"></i>
                                                    </div>
                                                    <h6 class="font-weight-bold mb-1">الشهادة الصحية</h6>
                                                    <p class="small text-muted mb-2">تنتهي في: <span
                                                            class="text-dark">{{ $employee->health_certificate_expiry ?? '---' }}</span>
                                                    </p>

                                                    <div class="mt-auto"> {{-- دفع الأزرار للأسفل لتبدأ من نفس الخط --}}
                                                        <div class="btn-group w-100">
                                                            <a href="{{ asset('storage/' . $employee->health_certificate_file) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye"></i> عرض
                                                            </a>
                                                            <a href="{{ asset('storage/' . $employee->health_certificate_file) }}"
                                                                download class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-download"></i> تحميل
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>

                                    {{-- رسالة في حال عدم وجود مرفقات --}}
                                    @if(!$employee->id_proof && !$employee->health_certificate_file)
                                        <div class="py-5 text-center">
                                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد ملفات مرفقة حالياً.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="spendTicketsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-ticket-alt mr-2"></i> عملية صرف تذاكر</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('ticket.transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-body p-4">
                    <div class="text-center mb-4 p-3 bg-light rounded border">
                        <span class="text-muted d-block">الرصيد المتاح لهذا الشهر</span>
                        <h1 class="display-4 font-weight-bold text-success mb-0">{{ $employee->currentMonthAllowance->current_balance ?? 0 }}</h1>
                        <small class="text-muted">تذكرة متبقية</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">عدد التذاكر المطلوب صرفها:</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="amount_spent" class="form-control text-center font-weight-bold" 
                                   max="{{ $employee->currentMonthAllowance->current_balance ?? 0 }}" min="1" required placeholder="0">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label>ملاحظات (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="سبب الصرف..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">تأكيد الخصم وتوليد الكود</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection