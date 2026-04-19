@extends('layouts.admin')
@section('title', 'تعديل الجزاء')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="card card-info"> {{-- غيرنا اللون للأزرق لتمييز التعديل عن الإضافة --}}
            <div class="card-header">
                <h3 class="card-title">تعديل بيانات الجزاء الخاص بـ: {{ $penalty->employee->full_name }}</h3>
            </div>
            
           <form action="{{ route('admin.penalties.update', $penalty->id) }}" method="POST">
    @csrf
    @method('PUT')
                <div class="card-body">
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
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $employee->id == $penalty->employee_id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تاريخ الجزاء <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ $penalty->date }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>خصم مالي (مبلغ)</label>
                                <input type="number" name="amount" class="form-control" value="{{ $penalty->amount }}" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>خصم من الرصيد (أيام)</label>
                                <input type="number" name="days_count" class="form-control" value="{{ $penalty->days_count }}">
                                <small class="text-warning">ملاحظة: تعديل أيام الخصم هنا لا يعدل رصيد الموظف تلقائياً (يفضل الحذف والإضافة لضبط الرصيد).</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>سبب الجزاء / الوصف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="3" required>{{ $penalty->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-info">تحديث البيانات</button>
                    <a href="{{ route('admin.penalties.index') }}" class="btn btn-default">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection