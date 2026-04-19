@extends('layouts.admin')

@section('title', 'تسجيل الحضور والانصراف')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6 text-right">
            <h1 class="m-0 text-dark" style="font-size: 1.5rem;">بصمة الموظف الإلكترونية</h1>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        
        {{-- عرض رسائل النجاح أو الخطأ --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-right" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- كرت ترحيب الموظف --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-body p-3 text-right">
                <div class="d-flex align-items-center flex-row-reverse">
                    <div class="bg-primary-light p-2 rounded-circle ml-3">
                        <i class="fas fa-user-circle text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-0">{{ $employee->full_name ?? auth()->user()->name }}</h6>
                        <small class="text-muted">الفرع: {{ $employee->branch->name ?? 'غير محدد' }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- الكرت الرئيسي للبصمة --}}
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
            
            @if($attendance && $attendance->signin_time && $attendance->signout_time)
                {{-- الحالة 1: أتم الدوام --}}
                <div class="bg-success py-5 text-center text-white" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;">
                    <i class="fas fa-check-double fa-4x mb-3 animate__animated animate__bounceIn"></i>
                    <h5 class="font-weight-bold">تم إكمال الدوام بنجاح!</h5>
                    <p class="mb-0 small opacity-75">نراك غداً على خير</p>
                </div>

            @else
                <div class="card-body text-center py-4">
                    {{-- عرض تنبيه في حالة الانصراف --}}
                    @if($attendance && $attendance->signin_time)
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="fas fa-sign-in-alt ml-1"></i> سجلت حضورك الساعة: <strong>{{ $attendance->signin_time }}</strong>
                        </div>
                    @endif

                    <h6 class="text-muted mb-3 font-weight-bold">تحديد الموقع الجغرافي</h6>
                    
                    {{-- مكان الخريطة --}}
                    <div id="map" style="height: 250px; width: 100%; border-radius: 15px; margin-bottom: 15px; border: 1px solid #eee;"></div>
                    
                    <div id="location-status" class="small text-muted mb-3">
                        <i class="fas fa-spinner fa-spin ml-1"></i> جاري جلب إحداثيات موقعك...
                    </div>

                    {{-- نموذج الإرسال (يتغير الأكشن بين الحضور والانصراف تلقائياً) --}}
                    <form id="attendance-form" action="{{ $attendance ? route('attendances.update', $attendance->id) : route('attendances.store') }}" method="POST">
                        @csrf
                        @if($attendance) @method('PUT') @endif
                        
                        <input type="hidden" name="lat" id="lat">
                        <input type="hidden" name="lng" id="lng">
                        
                        <button type="button" onclick="submitAttendance()" id="btn-submit" class="btn {{ $attendance ? 'btn-danger' : 'btn-primary' }} btn-lg btn-block shadow-lg" style="border-radius: 50px; padding: 15px; font-weight: bold;" disabled>
                            <i class="fas fa-fingerprint ml-2"></i> 
                            {{ $attendance ? 'بصمة انصراف الآن' : 'بصمة حضور الآن' }}
                        </button>
                    </form>
                </div>
            @endif

            <div class="card-footer bg-light py-2 text-center border-0">
                <small class="text-muted small">نظام HRMS - شركة المرح</small>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet Maps --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // استخدام var لتجنب أخطاء إعادة التعريف
    var myMap, myMarker, myCircle;

    // بيانات الفرع من السيرفر
    const branchLatitude = {{ $employee->branch->latitude ?? 0 }};
    const branchLongitude = {{ $employee->branch->longitude ?? 0 }};
    const branchRadius = {{ $employee->branch->radius_meters ?? 200 }};

    document.addEventListener('DOMContentLoaded', function() {
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            // تهيئة الخريطة
            myMap = L.map('map').setView([branchLatitude, branchLongitude], 16);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(myMap);

            // رسم نطاق الفرع
            myCircle = L.circle([branchLatitude, branchLongitude], {
                color: '#28a745',
                fillColor: '#28a745',
                fillOpacity: 0.1,
                radius: branchRadius
            }).addTo(myMap).bindPopup('نطاق الفرع المسموح');

            initLocationTracking();
        }
    });

   function initLocationTracking() {
    if (navigator.geolocation) {
        // إعدادات الدقة العالية والوقت
        const geoOptions = {
            enableHighAccuracy: true, // استخدام GPS حقيقي بدلاً من برج التغطية
            timeout: 15000,           // الانتظار لمدة 15 ثوانٍ كحد أقصى لجلب الإحداثيات
            maximumAge: 0            // إجبار المتصفح على جلب موقع جديد الآن (عدم استخدام الكاش)
        };

        navigator.geolocation.getCurrentPosition((position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            // تعبئة الحقول المخفية
            document.getElementById('lat').value = userLat;
            document.getElementById('lng').value = userLng;

            // تحديث الخريطة بموقع الموظف
            if (myMarker) myMap.removeLayer(myMarker);
            myMarker = L.marker([userLat, userLng]).addTo(myMap).bindPopup('موقعك الدقيق').openPopup();

            // جعل الخريطة تشمل الفرع والموظف معاً
            if (myCircle) {
                const featureGroup = new L.featureGroup([myMarker, myCircle]);
                myMap.fitBounds(featureGroup.getBounds().pad(0.3));
            }

            // تفعيل الزر وتغيير الرسالة
            document.getElementById('btn-submit').disabled = false;
            document.getElementById('location-status').innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> تم تحديد موقعك بدقة عالية</span>';

        }, (error) => {
            let errorMsg = "يرجى تفعيل الـ GPS للمتابعة";
            if (error.code === 3) errorMsg = "انتهت المهلة، حاول تحديث الصفحة";
            
            document.getElementById('location-status').innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> ${errorMsg}</span>`;
            console.error("Geolocation Error: ", error);
        }, geoOptions); // تم تمرير الإعدادات هنا
    }
}
    // الدالة المسؤولة عن إرسال النموذج
    function submitAttendance() {
        const actionBtn = document.getElementById('btn-submit');
        const actionForm = document.getElementById('attendance-form');
        
        if (actionForm) {
            actionBtn.disabled = true;
            actionBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إرسال البصمة...';
            actionForm.submit();
        }
    }
</script>
@stop