@extends('layouts.admin')

@section('title', 'تعديل مسمى وظيفي')

@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-info card-outline shadow">
                <div class="card-header bg-white">
                    <h3 class="card-title text-bold">تعديل: {{ $jobTitle->name }}</h3>
                </div>
                
                <form action="{{ route('job-titles.update', $jobTitle->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>القسم التابع له <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                <option value="">-- اختر القسم --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $jobTitle->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>اسم المسمى الوظيفي <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $jobTitle->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الراتب الأدنى</label>
                                    <input type="number" step="0.01" name="min_salary" class="form-control" value="{{ old('min_salary', $jobTitle->min_salary) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الراتب الأعلى</label>
                                    <input type="number" step="0.01" name="max_salary" class="form-control" value="{{ old('max_salary', $jobTitle->max_salary) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>الحالة</label>
                            <select name="status" class="form-control shadow-sm">
                                <option value="1" {{ old('status', $jobTitle->status) == 1 ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ old('status', $jobTitle->status) == 0 ? 'selected' : '' }}>متوقف</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>الوصف الوظيفي</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $jobTitle->description) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <button type="submit" class="btn btn-info px-4 shadow">
                            <i class="fas fa-save"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('job-titles.index') }}" class="btn btn-default border">إلغاء والعودة</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection