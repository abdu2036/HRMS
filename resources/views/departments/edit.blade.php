@extends('layouts.admin')

{{-- عنوان الصفحة في المتصفح --}}
@section('title', 'تعديل قسم: ' . $department->name)

{{-- عنوان المحتوى العلوي --}}
@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">تعديل بيانات القسم 📝</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">إدارة الأقسام</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
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
                        <h5><i class="icon fas fa-ban"></i> تنبيه! يرجى مراجعة الأخطاء التالية:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-info card-outline shadow"> {{-- غيرت اللون للأزرق السماوي لتمييز التعديل --}}
                    <div class="card-header">
                        <h3 class="card-title text-bold">تعديل: {{ $department->name }}</h3>
                    </div>

                    <form action="{{ route('departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="name">اسم القسم <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        id="name" value="{{ old('name', $department->name) }}" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="code">كود القسم <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                        id="code" value="{{ old('code', $department->code) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                {{-- حالة القسم مع الاختيار التلقائي --}}
                                <div class="col-md-6 form-group">
                                    <label>حالة القسم <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $department->status) == 1 ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ old('status', $department->status) == 0 ? 'selected' : '' }}>متوقف</option>
                                    </select>
                                </div>

                                {{-- إضافة حقل المدير ليكون متناسقاً مع باقي النظام --}}
                                <div class="col-md-6 form-group">
                                    <label for="manager_id">مدير القسم</label>
                                    <select name="manager_id" class="form-control select2 shadow-sm">
                                        <option value="">-- اختر مديراً --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('manager_id', $department->manager_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="parent_id">القسم الرئيسي (يتبع لـ)</label>
                                    <select name="parent_id" class="form-control select2 shadow-sm" id="parent_id">
                                        <option value="">-- قسم رئيسي (بدون أب) --</option>
                                        @foreach($parents as $parent)
                                            {{-- استثناء القسم نفسه من أن يكون أباً لنفسه --}}
                                            @if($parent->id != $department->id)
                                                <option value="{{ $parent->id }}" {{ old('parent_id', $department->parent_id) == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="order">ترتيب الظهور</label>
                                    <input type="number" name="order" class="form-control" id="order"
                                        value="{{ old('order', $department->order) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">وصف القسم</label>
                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $department->description) }}</textarea>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between">
                            <button type="submit" class="btn btn-info shadow">
                                <i class="fas fa-sync-alt"></i> تحديث البيانات
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