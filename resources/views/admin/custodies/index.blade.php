@extends('layouts.admin')

@section('title', 'سجل عهد الموظفين')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                <h5 class="mb-0">سجل عهد الموظفين 📦</h5>
                <a href="{{ route('custodies.create') }}" class="btn btn-primary btn-sm">إضافة عهدة +</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>تاريخ الاستلام</th> {{-- 🟢 العمود الجديد --}}
                                <th>العهدة</th>
                                <th>النوع</th>
                                <th>القيمة الأصلية</th>
                                <th>الحالة</th>
                                <th>العجز المسجل</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($custodies as $custody)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle"><strong>{{ $custody->employee->full_name }}</strong></td>
                                    
                                    {{-- 🟢 عرض التاريخ مع أيقونة --}}
                                    <td class="align-middle text-nowrap">
                                        <span class="text-muted small">
                                            <i class="far fa-calendar-alt"></i> 
                                            {{ $custody->created_at->format('Y-m-d') }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        {{ $custody->name }}
                                        @if($custody->type != 'financial')
                                            <br><small class="text-muted">{{ $custody->notes }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($custody->type == 'financial')
                                            <span class="badge badge-outline-info">مالية 💰</span>
                                        @elseif($custody->type == 'hardware')
                                            <span class="badge badge-outline-primary">جهاز 💻</span>
                                        @else
                                            <span class="badge badge-outline-purple" style="border: 1px solid purple; color: purple;">جهاز + مالية 🔄</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-primary font-weight-bold">
                                        {{ number_format($custody->amount, 2) }}</td>
                                    <td class="align-middle">
                                        @if($custody->status == 'received')
                                            <span class="badge bg-success p-2 text-white">تم الاستلام</span>
                                        @elseif($custody->status == 'deferred')
                                            <span class="badge bg-warning p-2 text-dark">
                                                <i class="fas fa-clock"></i> مؤجلة للشهر القادم
                                            </span>
                                        @elseif($custody->status == 'returned')
                                            <span class="badge bg-info p-2 text-white">تم الإرجاع</span>
                                        @elseif($custody->status == 'settled')
                                            <span class="badge bg-primary p-2 text-white">
                                                <i class="fas fa-check-double"></i> تم الخصم من المرتب
                                            </span>
                                        @else
                                            <span class="badge bg-danger p-2 text-white">عجز مالي</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-danger font-weight-bold">
                                        {{ number_format($custody->shortage_amount, 2) }}</td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-warning shadow-sm" data-toggle="modal"
                                            data-target="#shortageModal{{ $custody->id }}">
                                            تحديث الحالة / عجز
                                        </button>
                                        <a href="{{ route('custodies.print', $custody->id) }}" target="_blank"
                                            class="btn btn-sm btn-info shadow-sm">
                                            طباعة وصل 🖨️
                                        </a>
                                    </td>
                                </tr>

                                {{-- Modal تحديث الحالة (بقي كما هو) --}}
                                <div class="modal fade" id="shortageModal{{ $custody->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('custodies.updateStatus', $custody->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">إجراء على عهدة: {{ $custody->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-right" dir="rtl">
                                                    <p class="text-muted small">الموظف: {{ $custody->employee->full_name }}</p>
                                                    
                                                    <div class="form-group">
                                                        <label>تغيير الحالة</label>
                                                        <select name="status" class="form-control status-selector" data-id="{{ $custody->id }}" required>
                                                            <option value="received" {{ $custody->status == 'received' ? 'selected' : '' }}>مستلمة (تظهر في كشف المرتب)</option>
                                                            <option value="deferred" {{ $custody->status == 'deferred' ? 'selected' : '' }}>تأجيل (إخفاء من مرتب هذا الشهر 🛑)</option>
                                                            <option value="returned" {{ $custody->status == 'returned' ? 'selected' : '' }}>تم الإرجاع (إخلاء طرف)</option>
                                                            <option value="shortage" {{ $custody->status == 'shortage' ? 'selected' : '' }}>تسجيل عجز (خصم قيمة من الراتب)</option>
                                                            <option value="settled" {{ $custody->status == 'settled' ? 'selected' : '' }}>تم الخصم من المرتب مسبقاً</option>
                                                        </select>
                                                    </div>

                                                    <div id="shortage_input_div{{ $custody->id }}" class="form-group {{ $custody->status == 'shortage' ? '' : 'd-none' }}">
                                                        <label class="text-danger font-weight-bold">مبلغ العجز الفعلي 💰</label>
                                                        <input type="number" name="shortage_amount" class="form-control border-danger" step="0.01"
                                                            value="{{ $custody->shortage_amount }}" placeholder="أدخل القيمة المراد خصمها">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>ملاحظات إدارية 📝</label>
                                                        <textarea name="notes" class="form-control" rows="3" placeholder="اكتب سبب التأجيل أو العجز...">{{ $custody->notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-danger">تحديث وترحيل الحالة</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @empty
            {{-- 🟢 هذه الرسالة ستظهر فقط إذا كان الجدول فارغاً --}}
            <tr>
                <td colspan="9" class="text-center">
                    <div class="py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted" style="font-size: 1.2em;">لا يوجد عهد مسجلة حالياً</p>
                      </div>
                </td>
            </tr>
                   @endforelse
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection