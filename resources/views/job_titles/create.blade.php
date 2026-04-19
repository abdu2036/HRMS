@extends('layouts.admin')

@section('title', 'إضافة مسمى وظيفي جديد')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">إضافة مسمى وظيفي 👔</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-top-primary">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">بيانات الوظيفة الجديدة</h3>
                </div>
                
                <form action="{{ route('job-titles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="department_id">القسم التابع له <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                <option value="">-- اختر القسم --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">اسم المسمى الوظيفي <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="مثال: مدير موارد بشرية، مشغل ألعاب..." required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_salary">الراتب الأدنى</label>
                                    <input type="number" step="0.01" name="min_salary" id="min_salary" class="form-control" 
                                           value="{{ old('min_salary') }}" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_salary">الراتب الأعلى</label>
                                    <input type="number" step="0.01" name="max_salary" id="max_salary" class="form-control" 
                                           value="{{ old('max_salary') }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف المسمى الوظيفي</label>
                            <textarea name="description" id="description" class="form-control" rows="3" 
                                      placeholder="اكتب مهام الوظيفة هنا...">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('job-titles.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save"></i> حفظ المسمى
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection