@extends('layouts.admin')

@section('title', 'تعديل الشفت')

@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-info card-outline shadow">
                <div class="card-header bg-white">
                    <h3 class="card-title text-bold">تعديل بيانات الشفت: {{ $shift->name }} ⏰</h3>
                </div>
                
                <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>اسم الشفت <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $shift->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>وقت الحضور</label>
                                    <input type="time" name="start_time" class="form-control" 
                                           value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>وقت الانصراف</label>
                                    <input type="time" name="end_time" class="form-control" 
                                           value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>مدة الاستراحة (بالدقائق)</label>
                                    <input type="number" name="break_duration" class="form-control" 
                                           value="{{ old('break_duration', $shift->break_duration) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الحالة</label>
                                    <select name="status" class="form-control shadow-sm">
                                        <option value="1" {{ old('status', $shift->status) == 1 ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ old('status', $shift->status) == 0 ? 'selected' : '' }}>متوقف</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>وصف الشفت</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="اكتب ملاحظات حول هذا الشفت...">{{ old('description', $shift->description) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <button type="submit" class="btn btn-info px-4 shadow">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('shifts.index') }}" class="btn btn-default border">إلغاء والعودة</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection