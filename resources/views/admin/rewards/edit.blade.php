@extends('layouts.admin')

@section('title', 'تعديل المكافأة')

@section('content')
<div class="content">
    <div class="card card-outline card-primary shadow">
        <div class="card-header">
            <h3 class="card-title">تعديل بيانات مكافأة: {{ $reward->employee->full_name }}</h3>
        </div>
        <form action="{{ route('admin.rewards.update', $reward->id) }}" method="POST">
    @csrf 
    @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>المبلغ المالي</label>
                            <input type="number" name="amount" class="form-control" value="{{ $reward->amount }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>أيام الإجازة (تعديل الأيام لا يغير الرصيد السابق تلقائياً)</label>
                            <input type="number" name="days_count" class="form-control" value="{{ $reward->days_count }}" readonly>
                            <small class="text-danger">الأيام للقراءة فقط لضمان دقة الحسابات.</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>السبب</label>
                            <textarea name="description" class="form-control" rows="3">{{ $reward->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">تحديث البيانات</button>
               <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary">عودة</a>
            </div>
        </form>
    </div>
</div>
@endsection