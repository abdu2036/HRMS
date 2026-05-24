@extends('layouts.admin')
@section('title', 'إدارة الرواتب الأساسية')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">قائمة رواتب الموظفين</h3>
                <div class="card-tools">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryModal">
                        <i class="fas fa-plus"></i> إضافة راتب لموظف
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>اسم الموظف</th>
                            <th>الراتب الأساسي</th>
                            <th>البدلات</th>
                            <th>إجمالي الثابت</th>
                            <th>تاريخ التفعيل</th>
                            <th>عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->employee?->full_name ?? 'موظف غير موجود' }}</td>
                            <td>{{ number_format($salary->basic_salary, 2) }}</td>
                            <td>{{ number_format($salary->allowances, 2) }}</td>
                            <td class="text-bold text-success">
                                {{ number_format($salary->basic_salary + $salary->allowances, 2) }}
                            </td>
                            <td>{{ $salary->effective_date }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editSalary{{ $salary->id }}">
                                    <i class="fas fa-edit"></i> تعديل
                                </button>

                                <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editSalary{{ $salary->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">تعديل راتب: {{ $salary->employee->name }}</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form action="{{ route('admin.salaries.update', $salary->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>الراتب الأساسي</label>
                                                <input type="number" name="basic_salary" class="form-control" value="{{ $salary->basic_salary }}" step="0.01" required>
                                            </div>
                                            <div class="form-group">
                                                <label>البدلات الثابتة</label>
                                                <input type="number" name="allowances" class="form-control" value="{{ $salary->allowances }}" step="0.01">
                                            </div>
                                            <div class="form-group">
                                                <label>تاريخ التفعيل</label>
                                                <input type="date" name="effective_date" class="form-control" value="{{ $salary->effective_date }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-info">حفظ التعديلات</button>
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

<div class="modal fade" id="addSalaryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">تحديد راتب لموظف جديد</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.salaries.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>اختر الموظف</label>
                        <select name="employee_id" class="form-control" required>
                            @foreach(App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الراتب الأساسي</label>
                        <input type="number" name="basic_salary" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>البدلات الثابتة</label>
                        <input type="number" name="allowances" class="form-control" step="0.01" value="0">
                    </div>
                    <div class="form-group">
                        <label>تاريخ التفعيل</label>
                        <input type="date" name="effective_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection