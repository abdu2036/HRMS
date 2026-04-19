@extends('layouts.admin')

@section('title', 'تعديل بيانات الشركة')
@section('content_header', 'تعديل بروفايل الشركة')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h3 class="card-title">تحديث البيانات الأساسية ✏️</h3>
    </div>
    <form action="{{ route('company-profile.update', $company->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="company_name">اسم الشركة <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" id="company_name" value="{{ $company->company_name }}" required>
                </div>

                <div class="col-md-6 form-group">
                    <label for="company_logo">شعار الشركة 🖼️</label>
                    @if($company->company_logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $company->company_logo) }}" class="img-thumbnail" style="max-height: 80px;">
                            <small class="text-muted d-block">الشعار الحالي</small>
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" name="company_logo" class="custom-file-input" id="company_logo">
                        <label class="custom-file-label" for="company_logo">تغيير الشعار (اختياري)</label>
                    </div>
                </div>

                <div class="col-md-12 form-group">
                    <label for="address">العنوان بالتفصيل 📍</label>
                    <textarea name="address" class="form-control" rows="2">{{ $company->address }}</textarea>
                </div>

                <div class="col-md-4 form-group">
                    <label for="company_phone">رقم الهاتف 📞</label>
                    <input type="text" name="company_phone" class="form-control" value="{{ $company->company_phone }}">
                </div>

                <div class="col-md-4 form-group">
                    <label for="company_email">البريد الإلكتروني 📧</label>
                    <input type="email" name="company_email" class="form-control" value="{{ $company->company_email }}">
                </div>

                <div class="col-md-4 form-group">
                    <label for="website">الموقع الإلكتروني 🌐</label>
                    <input type="url" name="website" class="form-control" value="{{ $company->website }}">
                </div>
            </div>
        </div>

        <div class="card-footer text-left">
            <button type="submit" class="btn btn-warning">تحديث البيانات ✅</button>
            <a href="{{ route('company-profile.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection