<footer class="main-footer text-center bg-white border-top py-3">
    <div class="container-fluid">
        <strong>جميع الحقوق محفوظة &copy; {{ date('Y') }} <a href="#" class="text-primary">نظام إدارة الموارد البشرية</a>.</strong>
    </div>
</footer>

<style>
    /* هذا الجزء سيجعل الفوتر ينزل لأسفل الصفحة دائماً ويمنع التداخل */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .content-wrapper {
        flex: 1; /* يدفع الفوتر للأسفل حتى لو كان المحتوى قليلاً */
    }

    /* إخفاء أي نصوص زائدة قد تأتي من قوالب AdminLTE القديمة */
    .main-footer .float-right, 
    .main-footer .d-none {
        display: none !important;
    }
</style>