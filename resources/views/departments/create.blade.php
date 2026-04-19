@extends('layouts.admin')

{{-- عنوان الصفحة في المتصفح --}}
@section('title', 'إضافة قسم جديد')

{{-- عنوان المحتوى العلوي --}}
@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">إضافة قسم جديد 🏗️</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">إدارة الأقسام</a></li>
                    <li class="breadcrumb-item active">إضافة جديد</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

{{-- المحتوى الرئيسي --}}
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">

                {{-- عرض أخطاء التحقق --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> تنبيه! هناك أخطاء في المدخلات:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-primary card-outline shadow">
                    <div class="card-header">
                        <h3 class="card-title text-bold">بيانات القسم الجديد</h3>
                    </div>

                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                {{-- اسم القسم --}}
                                <div class="col-md-6 form-group">
                                    <label for="name">اسم القسم <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        id="name" placeholder="مثال: إدارة الموارد البشرية" value="{{ old('name') }}" required>
                                </div>

                                {{-- كود القسم --}}
                                <div class="col-md-6 form-group">
                                    <label for="code">كود القسم <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                        id="code" placeholder="مثال: HR-001" value="{{ old('code') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                {{-- حالة القسم --}}
                                <div class="col-md-6 form-group">
                                    <label>حالة القسم <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control shadow-sm">
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>متوقف</option>
                                    </select>
                                </div>

                                {{-- مدير القسم (جديد) --}}
                                <div class="col-md-6 form-group">
                                    <label for="manager_id">مدير القسم (اختياري)</label>
                                    <select name="manager_id" class="form-control select2 shadow-sm" id="manager_id">
                                        <option value="">-- اختر مديراً للقسم --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('manager_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                {{-- القسم الأب --}}
                                <div class="col-md-6 form-group">
                                    <label for="parent_id">يتبع لـ (القسم الرئيسي)</label>
                                    <select name="parent_id" class="form-control select2 shadow-sm" id="parent_id">
                                        <option value="">-- قسم رئيسي (جذر) --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">اتركه فارغاً إذا كان هذا القسم هو الإدارة العليا.</small>
                                </div>

                                {{-- ترتيب الظهور --}}
                                <div class="col-md-6 form-group">
                                    <label for="order">ترتيب الظهور في القائمة</label>
                                    <input type="number" name="order" class="form-control shadow-sm" id="order"
                                        placeholder="1, 2, 3..." value="{{ old('order', 1) }}">
                                </div>
                            </div>

                            {{-- وصف القسم --}}
                            <div class="form-group">
                                <label for="description">وصف أو ملاحظات</label>
                                <textarea name="description" class="form-control shadow-sm" id="description" rows="3"
                                    placeholder="اكتب هنا مهام القسم أو أي ملاحظات إضافية...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary shadow">
                                <i class="fas fa-save mr-1"></i> حفظ القسم الجديد
                            </button>
                            <a href="{{ route('departments.index') }}" class="btn btn-default border">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection