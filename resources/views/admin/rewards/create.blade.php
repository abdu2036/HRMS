@extends('layouts.admin')

@section('title', 'إضافة مكافأة جديدة')

@section('content')
    

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-success shadow">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">تفاصيل المكافأة</h3>
                </div>

                <form action="{{ route('admin.rewards.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            {{-- 1. اختيار الموظف --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id">اختيار الموظف <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id"
                                        class="form-control select2 @error('employee_id') is-invalid @enderror" required>
                                        <option value="">-- اختر الموظف --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->employee_code }} - {{ $emp->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- 2. التاريخ --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">تاريخ المكافأة <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                        value="{{ date('Y-m-d') }}" required>
                                    @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- 3. نوع المكافأة --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>نوع المكافأة</label>
                                    <select name="type" class="form-control">
                                        <option value="performance">أداء متميز</option>
                                        <option value="achievement">إنجاز مشروع</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>
                            </div>
                            {{-- 3. المبلغ المالي --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount"><i class="fas fa-money-bill-wave text-success"></i> المبلغ المالي
                                        (د.ل)</label>
                                    <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01"
                                        min="0" value="{{ old('amount', 0) }}">
                                    <small class="text-muted">اتركه 0 إذا كانت المكافأة أياماً فقط.</small>
                                </div>
                            </div>

                            {{-- 4. أيام الإجازة --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="days_count"><i class="fas fa-calendar-plus text-info"></i> أيام إجازة
                                        إضافية</label>
                                    <input type="number" name="days_count" class="form-control" placeholder="مثلاً: 1 أو 2"
                                        min="0" value="{{ old('days_count', 0) }}">
                                    <small class="text-muted">ستتم إضافة هذه الأيام مباشرة لرصيد الموظف الكلي.</small>
                                </div>
                            </div>

                            {{-- 5. وصف المكافأة (السبب) --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">سبب المكافأة <span class="text-danger">*</span></label>
                                    <textarea name="description"
                                        class="form-control @error('description') is-invalid @enderror" rows="3"
                                        placeholder="اكتب هنا تفاصيل التميز أو سبب منح المكافأة..."
                                        required>{{ old('description') }}</textarea>
                                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success shadow-sm px-4">
                            <i class="fas fa-check-circle"></i> حفظ ومنح المكافأة
                        </button>
                        <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary shadow-sm px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection