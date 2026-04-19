@extends('layouts.admin')

@section('title', 'لوحة التحكم الشخصية')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">مرحباً بك، {{ $employee->full_name }} 👋</h1>
        </div>
        <div class="col-sm-6 text-left">
            {{-- زر العودة لصفحة تسجيل الدخول --}}
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-sign-out-alt ml-1"></i> تسجيل الخروج
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@stop

@section('content')
<div class="container-fluid">
    {{-- 1. رسائل التميز والإحصائيات --}}
    @if($employee->attendances && $employee->attendances->where('late_minutes', 0)->count() > 20)
        <div class="alert alert-success alert-dismissible shadow-sm border-0">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-trophy"></i> بطل الشهر!</h5>
            سجلك نظيف من التأخير، أنت فخر لمنتزه المرح! استمر في هذا الأداء الرائع.
        </div>
    @endif

    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border">
                <span class="info-box-icon bg-warning text-white"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">تأخير شهر {{ now()->translatedFormat('F') }}</span>
                    <span class="info-box-number">{{ $totalLateHours }} ساعة</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border {{ $employee->total_leave_balance > 0 ? 'bg-success' : 'bg-secondary' }}">
                <span class="info-box-icon text-white"><i class="fas fa-umbrella-beach"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-white">رصيد الإجازات المتبقي</span>
                    <span class="info-box-number text-white">
                        {{ $employee->total_leave_balance > 0 ? $remainingBalance . ' يوم' : '0 يوم' }}
                    </span>
                </div>
            </div>
        </div>
<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box shadow-sm border bg-info">
        <span class="info-box-icon text-white"><i class="fas fa-ticket-alt"></i></span>
        <div class="info-box-content">
            <span class="info-box-text text-white">رصيد تذاكر الألعاب</span>
            <span class="info-box-number text-white">
    {{ $currentTicketAllowance->current_balance ?? 0 }} تذكرة
</span>
        </div>
    </div>
</div>
        
    </div>

    <div class="row">
        {{-- الجانب الأيمن: الملف الشخصي والطلبات الجديدة (مجمعة) --}}
        <div class="col-lg-4">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img src="{{ $employee->profile_photo ? asset('storage/' . $employee->profile_photo) : asset('assets/admin/dist/img/user2-160x160.jpg') }}"
                             class="profile-user-img img-fluid img-circle shadow-sm border-2"
                             style="width: 100px; height: 100px; object-fit: cover;" alt="صورة الموظف">
                    </div>
                    <h3 class="profile-username text-center mt-3">{{ $employee->full_name }}</h3>
                    <p class="text-muted text-center small">{{ $employee->job_title }}</p>
                    
                    <hr>

                    {{-- قسم الطلبات المنسدلة --}}
                    
                    {{-- 1. طلب إجازة --}}
                    <div class="card card-light collapsed-card shadow-none border mb-2">
                        <div class="card-header">
                            <h3 class="card-title text-primary"><i class="fas fa-paper-plane mr-2"></i> طلب إجازة جديد</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus text-primary"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('leaves.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <select name="leave_type" class="form-control form-control-sm" required>
                                        <option value="annual">سنوية (من الرصيد)</option>
                                        <option value="sick">مرضية</option>
                                        <option value="unpaid">بدون راتب</option>
                                    </select>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6"><input type="date" name="start_date" class="form-control form-control-sm" required></div>
                                    <div class="col-6"><input type="date" name="end_date" class="form-control form-control-sm" required></div>
                                </div>
                                <div class="form-group">
                                    <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="السبب..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">إرسال طلب الإجازة</button>
                            </form>
                        </div>
                    </div>

                    {{-- 2. طلب سلفة --}}
                    <div class="card card-light collapsed-card shadow-none border mb-2">
                        <div class="card-header">
                            <h3 class="card-title text-warning"><i class="fas fa-hand-holding-usd mr-2"></i> طلب سلفة مالية</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus text-warning"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.loans.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ auth()->user()->employee_id }}">
                                <input type="number" name="amount" class="form-control form-control-sm mb-2" placeholder="المبلغ المطلوب" required min="1">
                                <input type="number" name="installment" class="form-control form-control-sm mb-2" placeholder="القسط الشهري المقترح" required min="1">
                                <textarea name="reason" class="form-control form-control-sm mb-2" rows="2" placeholder="السبب..."></textarea>
                                <button type="submit" class="btn btn-warning btn-sm btn-block text-white">إرسال طلب السلفة</button>
                            </form>
                        </div>
                    </div>
                    {{-- 4. طلب تذاكر ألعاب --}}
<div class="card card-light collapsed-card shadow-none border mb-2">
    <div class="card-header">
        <h3 class="card-title text-info"><i class="fas fa-ticket-alt mr-2"></i> طلب تذاكر ألعاب</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus text-info"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
       <form action="{{ route('ticket.transactions.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>عدد التذاكر المطلوب</label>
        <input type="number" name="amount_spent" class="form-control" placeholder="10" required>
    </div>
    <div class="form-group">
        <label>ملاحظات</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="للأهل"></textarea>
    </div>
    <button type="submit" class="btn btn-info btn-block">إرسال الطلب</button>
</form>
    </div>
</div>

                    {{-- 3. تحديث بيانات الدخول --}}
                    <div class="card card-light collapsed-card shadow-none border mb-0">
                        <div class="card-header border-0">
                            <h3 class="card-title text-muted"><i class="fas fa-cog mr-2"></i> تحديث بيانات الدخول</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="photo" class="mb-2 d-block small">
                                <input type="password" name="password" class="form-control form-control-sm mb-2" placeholder="كلمة مرور جديدة">
                                <button type="submit" class="btn btn-sm btn-outline-secondary btn-block">تحديث</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجانب الأيسر: سجلات المتابعة (Tabs) --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header p-2 bg-white">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#leaves_tab" data-toggle="tab">إجازاتي</a></li>
                        <li class="nav-item"><a class="nav-link" href="#loans_tab" data-toggle="tab">طلبات السلف</a></li>
                        <li class="nav-item"><a class="nav-link" href="#rewards" data-toggle="tab">المكافآت</a></li>
                        <li class="nav-item"><a class="nav-link" href="#penalties" data-toggle="tab">العقوبات</a></li>
                        <li class="nav-item">
    <a class="nav-link" href="#tickets_tab" data-toggle="tab">تذاكر الألعاب</a>
</li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- تبويب الإجازات --}}
                        <div class="tab-pane active" id="leaves_tab">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light text-center">
                                    <tr><th>النوع</th><th>التاريخ</th><th>الأيام</th><th>الحالة</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->leaves as $leave)
                                        <tr class="text-center">
                                            <td>{{ $leave->leave_type == 'annual' ? 'سنوية' : ($leave->leave_type == 'sick' ? 'مرضية' : 'بدون راتب') }}</td>
                                            <td class="small">{{ $leave->start_date }}</td>
                                            <td>{{ $leave->days_count }}</td>
                                            <td>
                                                <span class="badge {{ $leave->status == 'approved' ? 'badge-success' : ($leave->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                                    {{ $leave->status == 'approved' ? 'تمت الموافقة' : ($leave->status == 'pending' ? 'انتظار' : 'مرفوض') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-3">لا توجد طلبات</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- تبويب السلف --}}
                        <div class="tab-pane" id="loans_tab">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light text-center">
                                    <tr><th>المبلغ</th><th>القسط</th><th>الحالة</th><th>رد الإدارة</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->loans as $loan)
                                        <tr class="text-center">
                                            <td>{{ $loan->amount }} د.ل</td>
                                            <td>{{ $loan->installment }}</td>
                                            <td>
                                                <span class="badge {{ $loan->status == 'active' ? 'badge-success' : ($loan->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                                    {{ $loan->status == 'active' ? 'مقبول ✅' : ($loan->status == 'pending' ? 'انتظار' : 'مرفوض') }}
                                                </span>
                                            </td>
                                            <td class="small">{{ $loan->admin_reply ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-3">لا توجد طلبات سلف</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
<div class="tab-pane" id="tickets_tab">
    <table class="table table-hover table-sm">
        <thead class="thead-light text-center">
            <tr>
                <th>الشهر</th>
                <th>الرصيد الممنوح</th>
                <th>المستهلك</th>
                <th>المتبقي</th>
            </tr>
        </thead>
        <tbody>
            @if($employee->currentMonthAllowance)
                <tr class="text-center">
                    <td>{{ now()->translatedFormat('F Y') }}</td>
                    <td>{{ $employee->currentMonthAllowance->initial_balance }}</td>
                    <td class="text-danger">{{ $employee->currentMonthAllowance->used_balance }}</td>
                    <td class="text-bold text-primary">{{ $employee->currentMonthAllowance->current_balance }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">لا توجد بيانات تذاكر متاحة</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
                        {{-- تبويب المكافآت --}}
                        <div class="tab-pane" id="rewards">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light"><tr><th>التاريخ</th><th>النوع</th><th>المبلغ</th></tr></thead>
                                <tbody>
                                    @forelse($rewards as $reward)
                                        <tr><td>{{ $reward->date }}</td><td>{{ $reward->type }}</td><td>{{ $reward->amount }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-3">لا توجد مكافآت</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- تبويب العقوبات --}}
                        <div class="tab-pane" id="penalties">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light"><tr><th>التاريخ</th><th>السبب</th><th>الإجراء</th></tr></thead>
                                <tbody>
                                    @forelse($penalties as $penalty)
                                        <tr>
                                            <td>{{ $penalty->date }}</td>
                                            <td class="text-danger">{{ $penalty->description }}</td>
                                            <td>{{ $penalty->type }}</td> 
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-3 text-muted small">السجل نظيف ✨</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop