<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>@yield('title') </title>
  @include('admin.includes.header')
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/mycustomstyle.css') }}">
  
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
  
  <style>
/* تنسيق شريط الخطوات الموحد للنظام */
.step-item { flex: 1; position: relative; text-align: center; }
.step-number { 
    width: 38px; height: 38px; line-height: 38px; display: inline-block; 
    border-radius: 50%; background: #e9ecef; color: #495057; font-weight: bold; 
    margin-bottom: 5px; transition: all 0.3s ease;
}
.step-item.active .step-number { 
    background: #007bff; color: white; box-shadow: 0 4px 10px rgba(0,123,255,0.3);
}
.step-item.active p { color: #007bff; font-weight: bold; }
.is-invalid {
    border: 2px solid #dc3545 !important;
}
.is-invalid {
    border: 1px solid #dc3545 !important;
    background-image: none !important; /* لإخفاء أيقونة التعجب إذا كانت تزعجك */
}

/* إضافة لمسة خفيفة عند التركيز على الحقل المخطئ */
.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* تنسيقات مخصصة لـ Select2 داخل المودال لتبدو متناسقة ودعم اللغة العربية */
.select2-container {
    z-index: 99999 !important; /* لضمان ظهور قائمة البحث فوق المودال دائماً */
}
.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}
.select2-search__field {
    direction: rtl !important;
    text-align: right !important;
}
.select2-results__option {
    text-align: right !important;
    direction: rtl !important;
}
</style> 

  @stack('css')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  @include('admin.includes.navbar')
  @include('admin.includes.sidebar')
  @include('admin.includes.content')
  @include('admin.includes.footer')
</div>
<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@stack('scripts')
@stack('js')

<script>
    function confirmPayroll(empId) {
        const methodSelector = document.getElementById('select_method_' + empId);
        const form = document.getElementById('form_' + empId);
        
        if (!methodSelector || !form) return;

        const methodVal = methodSelector.value;

        // 1. التحقق من اختيار طريقة الدفع
        if (methodVal === "" || methodVal === "#") {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يرجى اختيار طريقة الدفع أولاً',
                confirmButtonText: 'حسناً',
                customClass: { popup: 'swal2-rtl' }
            });
            methodSelector.classList.add('is-invalid');
            return;
        }

        // 2. تحديث الحقل المخفي
        const hiddenMethodInput = document.getElementById('input_method_' + empId);
        if (hiddenMethodInput) hiddenMethodInput.value = methodVal;

        // 3. جلب البيانات للرسالة
        const empName = form.closest('tr').cells[1].innerText.trim();
        const netSalary = document.getElementById('input_net_' + empId).value;
        const methodText = methodSelector.options[methodSelector.selectedIndex].text;

        // 4. إظهار مربع تأكيد احترافي (SweetAlert2)
  Swal.fire({
            title: '<div style="text-align: right; direction: rtl;">تأكيد عملية الصرف</div>',
            html: `
                <div style="text-align: right; direction: rtl; line-height: 2;">
                    <div style="display: flex; justify-content: flex-start; gap: 10px;">
                        <b>الموظف:</b> 
                        <span>${empName}</span>
                    </div>
                    <div style="display: flex; justify-content: flex-start; gap: 10px;">
                        <b>المبلغ الصافي:</b> 
                        <span style="color: #007bff; font-weight: bold;">${netSalary} د.ل</span>
                    </div>
                    <div style="display: flex; justify-content: flex-start; gap: 10px;">
                        <b>طريقة الدفع:</b> 
                        <span>${methodText}</span>
                    </div>
                    <hr>
                    <p style="text-align: center; margin-top: 15px;">هل تريد الاستمرار في عملية الاعتماد?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، اعتماد الآن',
            cancelButtonText: 'إلغاء',
            customClass: {
                popup: 'swal2-rtl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>

<script>
    // 1. تنبيه النجاح بعد الحفظ أو التعديل ✅
    @if(session('success'))
        Swal.fire({
            title: 'تمت العملية بنجاح!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'حسناً',
            timer: 3000 // يختفي تلقائياً بعد 3 ثوانٍ
        });
    @endif

    // 2. تنبيه الحذف  🗑️
    function confirmDelete(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
<script>
    // 1. تعريف المتغير في البداية ضروري جداً لتحديد الخطوة الحالية
    let currentStep = 1; 

    function nextPrev(n, formId, totalSteps) {
        // الوصول للعناصر الحالية
        const currentStepDiv = document.getElementById("step-" + currentStep);
        const currentIndicator = document.getElementById("step-" + currentStep + "-indicator");

        // التحقق من وجود الخطوة في الصفحة الحالية (لتجنب الأخطاء في الصفحات الأخرى)
        if (!currentStepDiv) return;

        // 2. التحقق من الحقول المطلوبة عند الضغط على "التالي"
        if (n === 1) {
            const inputs = currentStepDiv.querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.removeClass('is-invalid');
                }
            });

            if (!valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'تنبيه',
                    text: 'يرجى ملء جميع الحقول المطلوبة قبل الانتقال للخطوة التالية',
                    confirmButtonText: 'حسناً'
                });
                return false; 
            }
        }

        // 3. إرسال النموذج في الخطوة الأخيرة
        if (n === 1 && currentStep === totalSteps) {
            document.getElementById(formId).submit();
            return;
        }

        // 4. تنفيذ حركة التنقل
        currentStepDiv.classList.add("d-none");
        if(currentIndicator) currentIndicator.classList.remove("active");

        currentStep += n;

        const nextStepDiv = document.getElementById("step-" + currentStep);
        const nextIndicator = document.getElementById("step-" + currentStep + "-indicator");

        if(nextStepDiv) nextStepDiv.classList.remove("d-none");
        if(nextIndicator) nextIndicator.classList.add("active");

        // 5. تحديث الأزرار
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        if (prevBtn) prevBtn.classList.toggle("d-none", currentStep === 1);
        if (nextBtn) nextBtn.innerHTML = (currentStep === totalSteps) ? "حفظ البيانات" : "التالي";
    }

   $(function () {
    // تفعيل التلميحات
    if($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // الكود الجديد: إزالة الخطأ عند الكتابة أو التغيير
    $(document).on('input change', 'input, select, textarea', function() {
        if ($(this).val().trim() !== "") {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. تنبيه النجاح في منتصف الشاشة ✅
        @if(session('success'))
            Swal.fire({
                title: 'تمت العملية بنجاح!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'حسناً',
                timer: 3000,
                showConfirmButton: true,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-rtl' // لضمان اتجاه النص من اليمين لليسار
                }
            });
        @endif

        // 2. تنبيه الخطأ في منتصف الشاشة ❌
        @if(session('error'))
            Swal.fire({
                title: 'خطأ!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'حسناً',
                customClass: {
                    popup: 'swal2-rtl'
                }
            });
        @endif
    });
</script>

<style>
    /* تنسيق إضافي لضمان المحاذاة الصحيحة للغة العربية */
    .swal2-rtl {
        direction: rtl !important;
        text-align: center !important;
    }
    .swal2-title, .swal2-html-container {
        font-family: 'Cairo', sans-serif !important;
    }
</style>

</body>
</html>