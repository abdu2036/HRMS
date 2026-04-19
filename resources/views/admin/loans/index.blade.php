@extends('layouts.admin')

@section('title', 'إدارة السلف والقروض')

@section('content')
    <section class="content">
        <div class="container-fluid">

            {{-- صناديق الإحصائيات --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($loans->where('status', 'active')->sum('remaining_amount'), 2) }}</h3>
                            <p>إجمالي السلف النشطة (المعلقة)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title text-bold">سجل السلف والقروض</h3>
                    <div class="card-tools">
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addLoanModal">
                            <i class="fas fa-plus"></i> تسجيل سلفة جديدة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>المبلغ الإجمالي</th>
                                <th>القسط الشهري</th>
                                <th>المبلغ المتبقي</th>
                                <th>تاريخ البدء</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="text-bold">{{ $loan->employee->full_name ?? '---' }}</td>
                                    <td>{{ number_format($loan->amount, 2) }}</td>
                                    <td>{{ number_format($loan->installment, 2) }}</td>
                                    <td class="text-danger text-bold">{{ number_format($loan->remaining_amount, 2) }}</td>
                                    <td>{{ $loan->start_date }}</td>
                                    <td>
                                        @if($loan->status == 'pending')
                                            <span class="badge badge-warning text-dark">قيد الانتظار</span>
                                        @elseif($loan->status == 'active')
                                            <span class="badge badge-success">نشطة (مقبولة)</span>
                                        @elseif($loan->status == 'rejected')
                                            <span class="badge badge-danger">مرفوضة</span>
                                        @else
                                            <span class="badge badge-primary">تم السداد</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- أزرار التحكم --}}
                                        @if($loan->status == 'pending')
                                            <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="موافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#rejectModal{{ $loan->id }}" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        {{-- زر التعديل --}}
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#editLoan{{ $loan->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- زر الحذف المتصل بكود SweetAlert في الماستر --}}
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $loan->id }})" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- فورم الحذف المخفي --}}
                                        <form id="delete-form-{{ $loan->id }}"
                                            action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                {{-- مودال الرفض --}}
                                <div class="modal fade" id="rejectModal{{ $loan->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">رفض طلب سلفة: {{ $loan->employee->name }}</h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body text-right">
                                                    <label>سبب الرفض (اختياري)</label>
                                                    <textarea name="admin_reply" class="form-control" rows="3"
                                                        placeholder="اكتب سبب الرفض..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- مودال التعديل --}}
                                <div class="modal fade" id="editLoan{{ $loan->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content border-info">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">تعديل بيانات السلفة</h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-right">
                                                    <div class="form-group">
                                                        <label>إجمالي المبلغ</label>
                                                        <input type="number" name="amount" class="form-control"
                                                            value="{{ $loan->amount }}" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>القسط الشهري</label>
                                                        <input type="number" name="installment" class="form-control"
                                                            value="{{ $loan->installment }}" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>تاريخ البدء</label>
                                                        <input type="date" name="start_date" class="form-control"
                                                            value="{{ $loan->start_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-info">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- مودال إضافة سلفة جديدة --}}
    <div class="modal fade" id="addLoanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-bold">تسجيل سلفة موظف جديدة</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.loans.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-right">
                        {{-- تأكد أن هذا الكود داخل المودال الخاص بإضافة سلفة جديدة --}}
                        <div class="form-group">
                            <label>اختر الموظف</label>
                            <select name="employee_id" class="form-control select2" required style="width: 100%;">
                                <option value="">-- اختر الموظف --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }} ({{ $employee->employee_code ?? 'بدون كود' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المبلغ الإجمالي</label>
                                    <input type="number" name="amount" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>القسط الشهري</label>
                                    <input type="number" name="installment" class="form-control" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>تاريخ البدء</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning text-bold">حفظ السلفة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection