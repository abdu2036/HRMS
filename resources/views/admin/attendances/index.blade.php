@extends('layouts.admin')

@section('title', 'قائمة الحضور والانصراف')

@section('content')
    <section class="content text-right" dir="rtl" style="padding-top: 20px;">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info shadow-sm" onclick="handleBoxClick('all')" style="cursor: pointer;">
                        <div class="inner text-right">
                            <h3>{{ $attendances->count() }}</h3>
                            <p>إجمالي الحضور اليوم</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning shadow-sm" onclick="handleBoxClick('late')" style="cursor: pointer;">
                        <div class="inner text-right">
                            <h3 class="text-white">{{ $attendances->where('status', 'late')->count() }}</h3>
                            <p class="text-white">عدد المتأخرين</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <div class="small-box bg-success shadow-sm" onclick="handleBoxClick('signed-out')"
                        style="cursor: pointer;">
                        <div class="inner text-right">
                            <h3>{{ $attendances->whereNotNull('signout_time')->count() }}</h3>
                            <p>تم تسجيل الانصراف</p>
                        </div>
                        <div class="icon"><i class="fas fa-sign-out-alt"></i></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title float-right"><i class="fas fa-list ml-1"></i> سجلات الحضور العامة</h3>
                </div>

                <div class="card-body">
                    <div class="row align-items-end mb-4">
                        <div class="col-md-4">
                            <form action="{{ route('admin.attendances.index') }}" method="GET">
                                <label>عرض سجلات تاريخ:</label>
                                <div class="input-group">
                                    <input type="date" name="date" class="form-control"
                                        value="{{ request('date', now()->toDateString()) }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">تحديث</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4">
    <a href="{{ route('admin.attendances.monthly_summary', ['month' => request('date') ? date('Y-m', strtotime(request('date'))) : now()->format('Y-m')]) }}" 
       class="btn btn-outline-info btn-block shadow-sm"
       style="height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-file-invoice-dollar ml-2"></i>
        <strong>الانتقال لملخص الخصومات الشهري</strong>
    </a>
</div>
<div class="col-md-4">
    <a href="{{ route('admin.attendances.sync') }}" 
       class="btn btn-success btn-block shadow-sm"
       style="height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;"
       onclick="return confirm('هل تريد بدء سحب البصمات من الجهاز الآن؟ قد تستغرق العملية لحظات.')">
        <i class="fas fa-fingerprint ml-2"></i>
        <strong>سحب بصمات الجهاز (M3)</strong>
    </a>
</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">الموظف</th>
                                    <th>التاريخ</th>
                                    <th>الحضور</th>
                                    <th>الانصراف</th>
                                    <th>المسافة</th>
                                    <th>الحالة</th>
                                    <th>الموقع</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-right">
                                            <strong>{{ $att->employee->full_name }}</strong><br>
                                            <small class="text-muted"><i class="fas fa-building fa-xs"></i>
                                                {{ $att->employee->department->name ?? 'الموارد البشرية' }}</small>
                                        </td>
                                        <td><span class="text-secondary">{{ $att->date }}</span></td>
                                        <td>
    <span class="text-success font-weight-bold">{{ $att->signin_time }}</span>
    @if($att->late_minutes > 0)
        <br>
        <small class="badge badge-danger-light text-danger" style="background-color: #ffe5e5;">
            @php
                $h = floor($att->late_minutes / 60);
                $m = $att->late_minutes % 60;
            @endphp
            تأخير: {{ $h > 0 ? $h . 'س و ' : '' }}{{ $m }}د
        </small>
    @endif
</td><td>
    @if($att->signout_time)
        <span class="text-danger font-weight-bold">{{ $att->signout_time }}</span>
        @if($att->early_out_minutes > 0)
            <br>
            <small class="badge" style="background-color: #fcf3cf; color: #856404; border: 1px solid #f9e79f; padding: 2px 4px;">
                @php
                    $h = floor($att->early_out_minutes / 60);
                    $m = $att->early_out_minutes % 60;
                @endphp
                مبكير: {{ $h > 0 ? $h . 'س و ' : '' }}{{ $m }}د
            </small>
        @endif
    @else
        <span class="text-muted">--:--</span>
    @endif
</td>
                                        <td>
                                            <span class="text-primary"><i class="fas fa-route fa-xs"></i> مـ
                                                {{ round($att->distance_from_branch ?? 0) }}</span>
                                        </td>
                                        <td>
                                            @if($att->status == 'late')
                                                <span class="badge badge-warning"
                                                    style="color: #856404; background-color: #fff3cd;">متأخر</span>
                                            @else
                                                <span class="badge badge-success">منتظم</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="https://www.google.com/maps?q={{ $att->lat }},{{ $att->lng }}"
                                                target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>
                                        </td>
                                        <td>
    <button type="button" class="btn btn-sm btn-outline-warning" 
            data-toggle="modal" data-target="#earlyExitModal{{ $att->employee_id }}"
            title="إضافة إذن خروج مبكر">
        <i class="fas fa-door-open"></i> إذن خروج
    </button>
    
    <div class="modal fade" id="earlyExitModal{{ $att->employee_id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-right" dir="rtl">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">تسجيل إذن خروج مبكر: {{ $att->employee->full_name }}</h5>
                    <button type="button" class="close ml-0" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.early_exits.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="employee_id" value="{{ $att->employee_id }}">
                        <input type="hidden" name="date" value="{{ $att->date }}">
                        
                        <div class="form-group">
                            <label>يسمح له بالخروج ابتداءً من الساعة:</label>
                            <input type="time" name="allowed_exit_time" class="form-control" required>
                            <small class="text-muted">أي بصمة انصراف بعد هذا الوقت لن تُحسب كخروج مبكر.</small>
                        </div>

                        <div class="form-group">
                            <label>ملاحظات أو سبب الإذن:</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="اختياري..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">حفظ الإذن</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</td>
                                    </tr>
                                    
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-5 text-muted">لا توجد سجلات لهذا اليوم</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-right" dir="rtl">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">التفاصيل</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"
                        style="margin: -1rem auto -1rem -1rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-striped text-center mb-0">
                        <thead class="bg-light" id="modalHeader"></thead>
                        <tbody id="modalBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // استخدام json_encode مع الـ Raw لمنع تداخل الأقواس وتجنب خطأ Unclosed '['
        const globalAttendanceData = {!! json_encode($attendances->map(function ($att) {
        return [
            'name' => $att->employee->full_name,
            'status' => $att->status,
            'time' => $att->signin_time,
            'out_time' => $att->signout_time,
            'dept' => $att->employee->department->name ?? 'الموارد البشرية'
        ];
    })) !!};

        function handleBoxClick(type) {
            const body = document.getElementById('modalBody');
            const title = document.getElementById('modalTitle');
            const header = document.getElementById('modalHeader');

            if (!body || !title || !header) return;

            body.innerHTML = '';
            let filtered = [];

            if (type === 'all') {
                title.innerText = 'إحصائية جميع الحاضرين اليوم';
                filtered = globalAttendanceData;
            } else if (type === 'late') {
                title.innerText = 'قائمة الموظفين المتأخرين';
                filtered = globalAttendanceData.filter(a => a.status === 'late');
            } else if (type === 'signed-out') {
                title.innerText = 'قائمة الموظفين المنصرفين';
                filtered = globalAttendanceData.filter(a => a.out_time !== null && a.out_time !== '');
            }

            header.innerHTML = '<tr><th>الموظف</th><th>القسم</th><th>' + (type === 'signed-out' ? 'وقت الانصراف' : 'وقت الحضور') + '</th></tr>';

            if (filtered.length === 0) {
                body.innerHTML = '<tr><td colspan="3" class="py-4 text-muted">لا توجد بيانات متاحة</td></tr>';
            } else {
                filtered.forEach(item => {
                    let timeVal = (type === 'signed-out') ? item.out_time : item.time;
                    body.innerHTML += `
                        <tr>
                            <td class="text-right pr-4"><strong>${item.name}</strong></td>
                            <td>${item.dept}</td>
                            <td><span class="badge ${type === 'late' ? 'badge-danger' : 'badge-success'}">${timeVal ?? '--:--'}</span></td>
                        </tr>`;
                });
            }

            $('#attendanceModal').modal('show');
        }
    </script>
@endpush