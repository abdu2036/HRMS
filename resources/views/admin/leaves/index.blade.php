@extends('layouts.admin')

@section('title', 'إدارة طلبات الإجازات')

@section('content_header')
    <h1>سجل طلبات إجازات الموظفين</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- رسائل النجاح أو الخطأ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card card-outline card-primary shadow">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">طلبات الإجازة الواردة</h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $leaves->total() }} طلب إجمالي</span>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-valign-middle text-nowrap">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>النوع</th>
                        <th>الفترة (من - إلى)</th>
                        <th>المدة</th>
                        <th>السبب</th>
                        <th>الحالة</th>
                        <th class="text-center">القرار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $leave->employee->profile_photo ? asset('storage/' . $leave->employee->profile_photo) : asset('assets/img/default.png') }}" 
                                     class="img-circle mr-2" style="width: 35px; height: 35px; object-fit: cover;">
                                <span>{{ $leave->employee->full_name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($leave->leave_type == 'annual') <span class="badge bg-primary">سنوية</span>
                            @elseif($leave->leave_type == 'sick') <span class="badge bg-warning">مرضية</span>
                            @else <span class="badge bg-secondary">بدون راتب</span> @endif
                        </td>
                        <td>
                            <small class="text-muted"><i class="far fa-calendar-alt"></i> {{ $leave->start_date }}</small><br>
                            <small class="text-muted"><i class="far fa-calendar-check"></i> {{ $leave->end_date }}</small>
                        </td>
                        <td><span class="font-weight-bold text-indigo">{{ $leave->days_count }}</span> يوم</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-link text-info" data-toggle="tooltip" title="{{ $leave->reason ?? 'لا يوجد سبب مكتوب' }}">
                                <i class="fas fa-info-circle"></i> عرض السبب
                            </button>
                        </td>
                        <td>
                            @if($leave->status == 'pending') <span class="badge badge-info px-2 py-1"><i class="fas fa-spinner fa-spin mr-1"></i> قيد الانتظار</span>
                            @elseif($leave->status == 'approved') <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> مقبولة</span>
                            @else <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> مرفوضة</span> @endif
                        </td>
                        <td class="text-center">
                            @if($leave->status == 'pending')
                                {{-- زر الموافقة --}}
                                <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-outline-success shadow-sm" title="موافقة">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                
                                {{-- زر الرفض بفتح المودال --}}
                                <button class="btn btn-sm btn-outline-danger shadow-sm" data-toggle="modal" data-target="#rejectModal{{$leave->id}}" title="رفض">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <span class="text-muted small">تمت المعالجة</span>
                            @endif
                        </td>
                    </tr>

                    {{-- مودال الرفض لإدخال سبب الرفض --}}
                    <div class="modal fade" id="rejectModal{{$leave->id}}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">رفض طلب إجازة: {{ $leave->employee->full_name }}</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="status" value="rejected">
                                        <div class="form-group">
                                            <label>سبب الرفض (سيظهر للموظف)</label>
                                            <textarea name="admin_reply" class="form-control" rows="3" required placeholder="مثلاً: ضغط عمل، رصيد غير كافٍ..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- ترقيم الصفحات --}}
        @if($leaves->hasPages())
        <div class="card-footer clearfix">
            {{ $leaves->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endpush