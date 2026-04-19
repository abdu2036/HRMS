@extends('layouts.admin')
@section('title', 'إضافة جزاء جديد')

@section('content')


<section class="content">
    <div class="container-fluid">
        <div class="card card-danger"> {{-- تم تغيير اللون للأحمر هنا --}}
            <div class="card-header">
                <h3 class="card-title">نموذج تسجيل مخالفة / جزاء</h3>
            </div>
            
            <form action="{{ route('admin.penalties.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    {{-- عرض الأخطاء إن وجدت --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control select2" required>
                                    <option value="">-- اختر الموظف --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ الجزاء <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>خصم مالي (مبلغ)</label>
                                <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>خصم من الرصيد (أيام)</label>
                                <input type="number" name="days_count" class="form-control" placeholder="عدد الأيام المراد خصمها">
                                <small class="text-muted">سيتم خصم هذه الأيام تلقائياً من رصيد إجازات الموظف.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>سبب الجزاء / الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="3" placeholder="اكتب تفاصيل المخالفة هنا..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">حفظ الجزاء</button>
                    <a href="{{ route('admin.penalties.index') }}" class="btn btn-default">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection