@extends('layouts.admin')

@section('title', 'إضافة بيانات الشركة')
@section('content_header', 'إنشاء بروفايل الشركة الجديد')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h3 class="card-title">نموذج البيانات الأساسية 📝</h3>
    </div>
    <form action="{{ route('company-profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="company_name">اسم الشركة <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" id="company_name" required>
                </div>

                <div class="col-md-6 form-group">
                    <label for="company_logo">شعار الشركة 🖼️</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="company_logo" class="custom-file-input" id="company_logo">
                            <label class="custom-file-label" for="company_logo">اختر ملف الصورة</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 form-group">
                    <label for="address">العنوان بالتفصيل 📍</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-4 form-group">
                    <label for="company_phone">رقم الهاتف 📞</label>
                    <input type="text" name="company_phone" class="form-control">
                </div>

                <div class="col-md-4 form-group">
                    <label for="company_email">البريد الإلكتروني 📧</label>
                    <input type="email" name="company_email" class="form-control">
                </div>

                <div class="col-md-4 form-group">
                    <label for="website">الموقع الإلكتروني 🌐</label>
                    <input type="url" name="website" class="form-control" placeholder="https://example.com">
                </div>
            </div>
        </div>

        <div class="card-footer text-left">
            <button type="submit" class="btn btn-success">حفظ البيانات ✅</button>
            <a href="{{ route('company-profile.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection