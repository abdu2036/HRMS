@extends('layouts.admin')

{{-- تحديد العناوين الديناميكية --}}
@section('title', 'إضافة فرع جديد')
@section('content_header', 'إدارة الفروع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">بيانات الفرع الجديد 📝</h3>
                </div>
                
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- حقل اسم الفرع --}}
                        <div class="form-group">
                            <label for="name">اسم الفرع <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" placeholder="مثال: فرع طرابلس الرئيسي" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- حقل العنوان --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">العنوان</label>
                                    <input type="text" name="address" class="form-control" id="address" 
                                           placeholder="المدينة، الشارع..." value="{{ old('address') }}">
                                </div>
                            </div>

                            {{-- حقل الهاتف --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" name="phone" class="form-control" id="phone" 
                                           placeholder="09XXXXXXXX" value="{{ old('phone') }}">
                                </div>
                            </div>
                        </div>

                        <hr>
                        {{-- قسم الموقع الجغرافي --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary mb-0"><i class="fas fa-map-marker-alt"></i> تحديد موقع الفرع (GPS)</h5>
                            <button type="button" onclick="getCurrentLocation()" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-crosshairs"></i> جلب موقعي الحالي
                            </button>
                        </div>

                        <div id="location-status" class="alert alert-info d-none mb-3"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="latitude">خط العرض (Latitude)</label>
                                    <input type="text" name="latitude" id="latitude" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           value="{{ old('latitude') }}" placeholder="0.000000">
                                    @error('latitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="longitude">خط الطول (Longitude)</label>
                                    <input type="text" name="longitude" id="longitude" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           value="{{ old('longitude') }}" placeholder="0.000000">
                                    @error('longitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="radius_meters">نطاق البصمة (متر)</label>
                                    <input type="number" name="radius_meters" id="radius_meters" 
                                           class="form-control @error('radius_meters') is-invalid @enderror" 
                                           value="{{ old('radius_meters', 100) }}">
                                    @error('radius_meters')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ربط الفرع بالشركة --}}
                        <input type="hidden" name="company_profile_id" value="{{ $company->id }}">

                    </div>

                    <div class="card-footer text-left">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ البيانات
                        </button>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- نفس كود الجافاسكريبت المستخدم في التعديل --}}
<script>
function getCurrentLocation() {
    const statusDiv = document.getElementById('location-status');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
    statusDiv.classList.add('alert-info');
    statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تحديد الموقع...';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            latInput.value = position.coords.latitude;
            lngInput.value = position.coords.longitude;
            statusDiv.classList.replace('alert-info', 'alert-success');
            statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> تم التقاط الموقع بنجاح.';
        }, function(error) {
            statusDiv.classList.replace('alert-info', 'alert-danger');
            statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> فشل جلب الموقع، يرجى إدخاله يدوياً.';
        }, { enableHighAccuracy: true });
    } else {
        statusDiv.innerHTML = "المتصفح لا يدعم GPS.";
    }
}
</script>
@endsection