<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController, RoleController, UserController, BranchController,
    CompanyProfileController, DepartmentController, JobTitleController,
    ShiftController, EmployeeController, AttendanceController, EmployeeDashboardController,
     AttendanceDeviceController,
      LeaveController ,
        PenaltyController, 
        SalaryController,
         LoanController,
         CustodyController,
         FinancialTransactionController,
         PayrollController,
         PayrollReportController,
         TicketTransactionController,
        

        
};
use App\Http\Controllers\CorrespondenceController;
use App\Http\Controllers\RewardController;


// 1. المسارات العامة
Route::get('/', fn() => redirect()->route('login'));

// 2. مسار التوزيع الذكي (Dashboard)
// هذا المسار سيعمل كـ "محطة توزيع" بناءً على ما عدلناه في الـ Controller
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. مسارات الملف الشخصي العام (للكل)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. مسارات الإدارة العليا (super-admin)
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::resource('company-profile', CompanyProfileController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

// 5. مجموعة مسارات الإدارة (admin prefix) - تم تعديلها لتشمل كل ما يخص الموارد البشرية في مكان واحد للتعديل عليها هنا 
Route::middleware(['auth', 'role:super-admin|Accountant' ])->prefix('admin')->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('job-titles', JobTitleController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('employees', EmployeeController::class);
    
    // مسار إنشاء حساب للموظف
    Route::get('/employees/{id}/create-account', [EmployeeController::class, 'createAccount'])->name('employees.createAccount');
    
    // تقارير الحضور للمدير
    Route::get('/attendances-report', [AttendanceController::class, 'adminIndex'])->name('admin.attendances.index');
});

// 6. مجموعة مسارات الموظف الموحدة (is_employee)
// وضعنا هنا كل ما يحتاجه الموظف في مكان واحد
Route::middleware(['auth', 'is_employee'])->group(function () { 
    // لوحة التحكم الشخصية للموظف
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    // مسارات البصمة (الحضور والانصراف)
    Route::get('/my-attendance', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/store', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::put('/attendances/update/{id}', [AttendanceController::class, 'update'])->name('attendances.update');
    // تحديث بيانات الموظف من صفحته الشخصية
    Route::post('/employee/profile/update', [EmployeeDashboardController::class, 'updateProfile'])->name('employee.profile.update');
    // رابط صفحة ملخص الحضور الشهري
});


// بدلاً من ['middleware' => 'admin']
Route::middleware(['auth', 'role:super-admin|Accountant'])->group(function () {
    Route::get('/admin/attendances/monthly-summary', [AttendanceController::class, 'monthlySummary'])
         ->name('admin.attendances.monthly_summary');
});

//خاص بجهاز البصمة في المنتزه
Route::get('/device/test', [AttendanceDeviceController::class, 'testConnection']);


// 7. مسارات الإجازات
// للموظف
// داخل مجموعة الموظف (المجموعة رقم 6 في كودك)
Route::middleware(['auth', 'is_employee'])->group(function () {
    Route::post('/leaves/store', [LeaveController::class, 'store'])->name('leaves.store'); // تقديم طلب
});

// داخل مجموعة الإدارة (المجموعة رقم 5 في كودك)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/leaves', [LeaveController::class, 'index'])->name('admin.leaves.index'); // عرض الطلبات
    Route::post('/leaves/update-status/{id}', [LeaveController::class, 'updateStatus'])->name('leaves.updateStatus'); // قبول/رفض
    Route::get('/leaves/events', [LeaveController::class, 'getEvents'])->name('admin.leaves.events');
    // أضف هذا المسار داخل مجموعة الأدمن في web.php
Route::get('/admin/leaves/calendar', function() {
    return view('admin.leaves.calendar');
})->name('admin.leaves.calendar');

});
// رابط طباعة بطاقة تعريف الموظف
Route::get('/admin/employees/{id}/id-card', [EmployeeController::class, 'idCard'])->name('admin.employees.id_card');

//مسار المكافآت
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/create', [RewardController::class, 'create'])->name('rewards.create');
    Route::post('/rewards/store', [RewardController::class, 'store'])->name('rewards.store');
    Route::get('/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('rewards.edit');
    Route::delete('/rewards/{reward}', [RewardController::class, 'destroy'])->name('rewards.destroy');
    Route::put('/rewards/{reward}/update', [RewardController::class, 'update'])->name('rewards.update'); 
});
//العقوبات
// 1. مجموعة العقوبات المحمية (مقتصرة على مدير النظام والمحاسب)
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin')->name('admin.')->group(function () {       
    
    // سطر الـ resource هذا يغنيك عن كتابة (index, create, store, edit, destroy) يدوياً ويقوم بإنشائها تلقائياً
    Route::resource('penalties', PenaltyController::class);

});

// 2. مسار قراءة الإشعار (يمكن لأي موظف قراءته، لكن فقط المدير والمحاسب يمكنهم إنشاء العقوبات والإشعارات)
Route::middleware(['auth'])->group(function () {
    
    Route::get('admin/notifications/{id}/read', [PenaltyController::class, 'readNotification'])
         ->name('admin.notifications.read');
});


//الاشعارات
Route::get('/notifications/all', [PenaltyController::class, 'allNotifications'])->name('admin.notifications.all');

//روابط الرواتب
// مجموعة مسارات الرواتب - محمية ومقتصرة على مدير النظام والمحاسب فقط
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin')->name('admin.')->group(function () {
    
    // سطر الـ resource يغطي (index, create, store, show, edit, update, destroy)
    Route::resource('salaries', SalaryController::class);
    
    // مسار اعتماد وصرف الراتب (تم حذف /admin/ المكررة لتناسق الرابط)
    Route::post('salaries/approve/{id}', [SalaryController::class, 'approvePayment'])->name('salaries.approve');
    
});
// روابط السلف
// إضافة name('admin.') تجعل كل المسارات بالداخل تبدأ بـ admin.
Route::prefix('admin')->name('admin.')->group(function () {
    // هذا السطر وحده يكفي (ينشئ: index, create, store, show, edit, update, destroy)
    Route::resource('loans', LoanController::class);
    // المسارات الإضافية الخاصة بك (تأكد من مطابقتها للكنترولر)
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
});

// رابط الموافقة على السلفة من قبل المدير
Route::post('admin/loans/{id}/approve', [LoanController::class, 'approve'])->name('loans.approve');
// مسارات إدارة السلف للمدير
Route::post('/admin/loans/{id}/approve', [LoanController::class, 'approve'])->name('admin.loans.approve');
Route::post('/admin/loans/{id}/reject', [LoanController::class, 'reject'])->name('admin.loans.reject');


//العهد
Route::middleware(['auth', 'role:super-admin|Accountant'])->group(function () {
    Route::prefix('custodies')->name('custodies.')->group(function () {
        Route::get('/', [CustodyController::class, 'index'])->name('index');
        Route::get('/create', [CustodyController::class, 'create'])->name('create');
        Route::post('/', [CustodyController::class, 'store'])->name('store');
        Route::put('/{id}/update-status', [CustodyController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{id}/print', [CustodyController::class, 'printReceipt'])->name('print');
    });
});

  


// مسارات الحاوية المالية
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('financial-transactions', [FinancialTransactionController::class, 'index'])->name('financial.index');
    Route::delete('financial-transactions/{id}', [FinancialTransactionController::class, 'destroy'])->name('financial.destroy');
    
});

// روابط صرف المرتبات النهائية
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin')->name('admin.')->group(function () {
    // رابط عرض جدول الرواتب -> يصبح اسمه admin.payroll.index
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    // رابط معالجة عملية الصرف -> يصبح اسمه admin.payroll.process
    Route::post('/payroll/process', [PayrollController::class, 'process'])->name('payroll.process');
    // التعديل هنا: حذفنا admin. من الاسم لأنها مضافة تلقائياً من الـ Group
    Route::get('/payroll/receipt/{id}', [PayrollController::class, 'showReceipt'])->name('payroll.receipt');
   Route::get('/payroll-reports/print', [PayrollReportController::class, 'print'])->name('payroll.reports.print');
});

// مسارات كشف المرتبات المستقل (التقارير)
Route::middleware(['auth', 'role:super-admin|Accountant'])->prefix('admin/payroll-reports')->group(function () {
    // 1. عرض التقرير النهائي (كشف مرتبات الموظفين)
    Route::get('/', [PayrollReportController::class, 'index'])->name('payroll.reports.index');
    // 2. استقبال عملية الترحيل والحفظ من شاشة الصرف
    Route::post('/store', [PayrollReportController::class, 'store'])->name('payroll.reports.store');
    // 3. مسار اختياري لعرض موظف واحد فقط (إيصال) إذا احتجت له مستقبلاً
    Route::get('/{id}', [PayrollReportController::class, 'show'])->name('payroll.reports.show');
    Route::get('/receipt/{id}', [PayrollReportController::class, 'showReceipt'])->name('payroll.reports.showReceipt');
});

// رابط تغيير حالة العهدة (تسليم/استلام)
Route::post('/admin/custodies/toggle-status/{id}', [App\Http\Controllers\CustodyController::class, 'toggleStatus'])->name('admin.custodies.toggleStatus');
// رابط تصدير الحضور إلى Excel
Route::get('attendances/export', [AttendanceController::class, 'exportExcel'])->name('admin.attendances.export');



//المسارات المراسلات الإدارية
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // مسارات المراسلات الإدارية
    Route::prefix('correspondence')->name('correspondence.')->group(function () {
        
        // عرض قائمة المراسلات (الوارد والصادر)
        Route::get('/', [CorrespondenceController::class, 'index'])->name('index');
        
        // واجهة إنشاء مراسلة جديدة
        Route::get('/create', [CorrespondenceController::class, 'create'])->name('create');
        
        // حفظ المراسلة الجديدة
        Route::post('/store', [CorrespondenceController::class, 'store'])->name('store');
        
        // عرض تفاصيل مراسلة محددة (لقراءتها)
        Route::get('/{id}', [CorrespondenceController::class, 'show'])->name('show');
        
        // طباعة المراسلة الرسمية (الفورم الرسمي)
        Route::get('/{id}/print', [CorrespondenceController::class, 'print'])->name('print');

    });

//رابط التذاكر
Route::post('/ticket-transactions', [TicketTransactionController::class, 'store'])->name('ticket.transactions.store');
// مسار إرسال طلب التذاكر
Route::post('/employee/tickets/request', [TicketTransactionController::class, 'storeRequest'])->name('tickets.request');

});

// رابط الموافقة على طلب امغادرة من قبل المدير
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/early-exits/store', [AttendanceController::class, 'storeEarlyExit'])->name('admin.early_exits.store');
});

// رابط ترحيل الخصومات الشهرية من الحضور إلى الرواتب
Route::middleware(['auth', 'role:super-admin|Accountant'])->group(function () {
  Route::post('/admin/attendances/transfer-deductions', [AttendanceController::class, 'transferMonthlyDeductions'])
    ->name('admin.attendances.transfer_deductions');
});


//رابط جهاز البصمة
Route::get('/attendances/sync', [AttendanceDeviceController::class, 'syncAttendance'])->name('admin.attendances.sync');
require __DIR__.'/auth.php';