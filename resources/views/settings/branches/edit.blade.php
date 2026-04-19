@extends('layouts.admin')

{{-- تحديد العناوين الديناميكية --}}
@section('title', 'تعديل بيانات الفرع والموقع الجغرافي')
@section('content_header', 'إدارة الفروع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-warning shadow"> {{-- التعبير عن التعديل باللون الأصفر --}}
                <div class="card-header">
                    <h3 class="card-title">تعديل الفرع: {{ $branch->name }} ✏️</h3>
                </div>
                
                <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                    @csrf
                    @method('PUT') 
                    
                    <div class="card-body">
                        
                        {{-- حقل اسم الفرع --}}
                        <div class="form-group">
                            <label for="name">اسم الفرع <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" value="{{ old('name', $branch->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- حقل العنوان --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">العنوان <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control" id="address" 
                                           value="{{ old('address', $branch->address) }}" required>
                                </div>
                            </div>

                            {{-- حقل الهاتف --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" id="phone" 
                                           value="{{ old('phone', $branch->phone) }}" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary mb-0"><i class="fas fa-map-marker-alt"></i> إعدادات الموقع الجغرافي (GPS)</h5>
                            {{-- زر جلب الموقع الحالي --}}
                            <button type="button" onclick="getCurrentLocation()" class="btn btn-sm btn-success">
                                <i class="fas fa-crosshairs"></i> جلب موقعي الحالي الآن
                            </button>
                        </div>
                        <p class="text-muted small">يمكنك إدخال الإحداثيات يدوياً أو الوقوف في مقر الفرع والضغط على زر "جلب موقعي".</p>

                        <div id="location-status" class="alert alert-info d-none mb-3">
                            {{-- سيظهر هنا حالة جلب الموقع --}}
                        </div>

                        <div class="row">
                            {{-- حقل خط العرض --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="latitude">خط العرض (Latitude)</label>
                                    <input type="text" name="latitude" id="latitude" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           value="{{ old('latitude', $branch->latitude) }}" placeholder="سيتم جلبه تلقائياً">
                                    @error('latitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- حقل خط الطول --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="longitude">خط الطول (Longitude)</label>
                                    <input type="text" name="longitude" id="longitude" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           value="{{ old('longitude', $branch->longitude) }}" placeholder="سيتم جلبه تلقائياً">
                                    @error('longitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- نطاق السماح بالامتار --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="radius_meters">نطاق البصمة (متر)</label>
                                    <input type="number" name="radius_meters" id="radius_meters" 
                                           class="form-control @error('radius_meters') is-invalid @enderror" 
                                           value="{{ old('radius_meters', $branch->radius_meters ?? 100) }}">
                                    @error('radius_meters')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- الحقل المخفي للشركة --}}
                        <input type="hidden" name="company_profile_id" value="{{ $branch->company_profile_id }}">

                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary">إلغاء والعودة</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- كود الجافاسكريبت لجلب الموقع --}}
<script>
function getCurrentLocation() {
    const statusDiv = document.getElementById('location-status');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // إظهار صندوق الحالة
    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
    statusDiv.classList.add('alert-info');
    statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تحديد إحداثيات موقعك الحالي...';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // نجاح جلب الموقع
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            latInput.value = lat;
            lngInput.value = lng;

            statusDiv.classList.replace('alert-info', 'alert-success');
            statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> تم جلب الإحداثيات بنجاح وتمت تعبئة الحقول.';
        }, function(error) {
            // فشل جلب الموقع
            statusDiv.classList.replace('alert-info', 'alert-danger');
            let message = "حدث خطأ أثناء جلب الموقع.";
            if(error.code == 1) message = "يرجى السماح للمتصفح بالوصول إلى الموقع (Permission Denied).";
            statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
        }, {
            enableHighAccuracy: true
        });
    } else {
        statusDiv.classList.replace('alert-info', 'alert-danger');
        statusDiv.innerHTML = "متصفحك لا يدعم جلب الموقع الجغرافي.";
    }
}
</script>
@endsection