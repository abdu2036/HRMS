@extends('layouts.admin')
@section('title', 'تقارير الرواتب الأساسية والوظائف')

@section('content')

<section class="content pt-3">
    <div class="container-fluid">
        
        <div id="reportArea">
            
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3>{{ $totalEmployeesCount }}</h3>
                            <p>عدد الموظفين المسجلين</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary shadow-sm text-white">
                        <div class="inner">
                            <h3>{{ number_format($totalBasicSalaries, 2) }} <small class="text-xs">د.ل</small></h3>
                            <p>إجمالي الرواتب الأساسية</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalAllowances, 2) }} <small class="text-xs">د.ل</small></h3>
                            <p>إجمالي البدلات الثابتة</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3>{{ number_format($totalNetSalaries, 2) }} <small class="text-xs">د.ل</small></h3>
                            <p>إجمالي ميزانية الرواتب الثابتة</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title text-bold"><i class="fas fa-file-invoice-dollar mr-1"></i> كشف الرواتب والوظائف العام للمنشأة</h3>
                    <div class="card-tools">
                        <button class="btn btn-default btn-sm mr-2" onclick="printReportData();">
                            <i class="fas fa-print"></i> طباعة التقرير الحالي
                        </button>
                        @role('super-admin')
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSalaryModal">
                            <i class="fas fa-plus"></i> إضافة راتب لموظف
                        </button>
                        @endrole
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-striped text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الموظف</th>
                                <th>الوظيفة</th>
                                <th>الراتب الأساسي</th>
                                <th>البدلات الثابتة</th>
                                <th class="bg-dark text-white print-dark-th">إجمالي الراتب الثابت</th>
                                <th>تاريخ التفعيل</th>
                                <th class="hide-action-column" style="width: 120px;">خيارات إدارية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaries as $salary)
                            <tr>
                                <td class="text-bold text-left">{{ $salary->id }}</td>
                                <td class="text-bold text-left">{{ $salary->employee?->full_name ?? 'موظف غير موجود' }}</td>
                                <td>
    <span class="badge badge-secondary p-2">
        {{ $salary->employee?->jobTitle?->name ?? 'غير محدد' }}
    </span>
</td>
                                <td>{{ number_format($salary->basic_salary, 2) }}</td>
                                <td>{{ number_format($salary->allowances, 2) }}</td>
                                <td class="text-bold text-success bg-light text-lg">
                                    {{ number_format($salary->net_salary, 2) }} د.ل
                                </td>
                                <td>{{ $salary->effective_date }}</td>
                                <td class="hide-action-column">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editSalary{{ $salary->id }}" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                     @role('super-admin')
                                    <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف سجل راتب الموظف؟')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                        @endrole
                                </td>
                            </tr>

                            <div class="modal fade" id="editSalary{{ $salary->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">تعديل راتب: {{ $salary->employee?->full_name }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <form action="{{ route('admin.salaries.update', $salary->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body text-right">
                                                <div class="form-group">
                                                    <label>الراتب الأساسي</label>
                                                    <input type="number" name="basic_salary" class="form-control" value="{{ $salary->basic_salary }}" step="0.01" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>البدلات الثابتة</label>
                                                    <input type="number" name="allowances" class="form-control" value="{{ $salary->allowances }}" step="0.01">
                                                </div>
                                                <div class="form-group">
                                                    <label>تاريخ التفعيل</label>
                                                    <input type="date" name="effective_date" class="form-control" value="{{ $salary->effective_date }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-info">حفظ التعديلات</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> </div>
</section>

<div class="modal fade" id="addSalaryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">تحديد راتب لموظف جديد</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.salaries.store') }}" method="POST">
                @csrf
                <div class="modal-body text-right">
                    <div class="form-group">
                        <label>اختر الموظف</label>
                        <select name="employee_id" class="form-control" required>
                            @foreach(App\Models\Employee::whereDoesntHave('salary')->get() as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} [{{ $emp->position ?? 'بدون وظيفة' }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الراتب الأساسي</label>
                        <input type="number" name="basic_salary" class="form-control" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>البدلات الثابتة</label>
                        <input type="number" name="allowances" class="form-control" step="0.01" value="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>تاريخ التفعيل</label>
                        <input type="date" name="effective_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printReportData() {
    // 1. استخلاص كود الكروت والجدول فقط من الصفحة الحالية
    var reportHtml = document.getElementById('reportArea').innerHTML;
    
    // 2. إنشاء نافذة منبثقة معزولة عن نظام وتنسيق القالب الأصلي لمنع الإخفاء
    var printWindow = window.open('', '', 'height=700,width=900');
    
    printWindow.document.write('<html><head><title>تقرير ميزانية الرواتب والوظائف العام</title>');
    
    // ربط مكتبة الـ Bootstrap المباشرة ليعود التصميم بألوانه وجداوله الفخمة
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">');
    
    // حقن كود CSS مخصص لتهيئة وحماية مظهر التقرير داخل الورقة
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: "Cairo", sans-serif; padding: 30px; direction: rtl; text-align: right; background-color: #fff !important; }');
    printWindow.document.write('.table { width: 100% !important; margin-top: 20px; border-collapse: collapse !important; }');
    printWindow.document.write('.table th, .table td { border: 1px solid #000 !important; padding: 10px 6px !important; text-align: center !important; vertical-align: middle !important; color: #000 !important; }');
    printWindow.document.write('.text-right { text-align: right !important; }');
    printWindow.document.write('.text-bold { font-weight: bold !important; }');
    printWindow.document.write('.badge { border: 1px solid #777 !important; background: #f0f0f0 !important; color: #000 !important; padding: 5px; }');
    
    // إخفاء أزرار العمليات (التعديل والحذف وزر الطباعة العلوي) إجبارياً داخل التقرير المطبوع حتى لا تظهر في الورقة
    printWindow.document.write('.hide-action-column, .btn, .card-tools, .card-header .btn { display: none !important; }');
    
    // فرض طباعة الألوان والخلفيات وجعل خلفية خانة الإجمالي داكنة وواضحة
    printWindow.document.write('.print-dark-th { background-color: #000 !important; color: #fff !important; }');
    printWindow.document.write('@media print { body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } }');
    printWindow.document.write('</style></head><body>');
    
    // 3. إضافة ترويسة تقرير رسمية ومنظمة جداً في أعلى الورقة المطبوعة
    printWindow.document.write('<div style="text-align: center; margin-bottom: 25px;">');
    printWindow.document.write('<h2 style="font-weight: bold; margin-bottom: 5px;">منظومة إدارة الموظفين والرواتب</h2>');
    printWindow.document.write('<h4 style="color: #555;">كشف تقرير إجمالي الرواتب والوظائف العام للمنشأة</h4>');
    printWindow.document.write('<p style="font-size: 13px;">تاريخ إصدار التقرير: ' + new Date().toISOString().slice(0,10) + '</p>');
    printWindow.document.write('<hr style="border-top: 2px dashed #000; margin-top: 15px;">');
    printWindow.document.write('</div>');
    
    // 4. ضخ البيانات المنسوخة (الكروت والجدول)
    printWindow.document.write(reportHtml);
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    
    // 5. إعطاء المتصفح لحظات بسيطة لتحميل التنسيق ثم إظهار شاشة الطباعة فوراً وبشكل ممتلئ
    setTimeout(function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 400);
}
</script>

@endsection