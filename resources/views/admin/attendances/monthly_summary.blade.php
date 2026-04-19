@extends('layouts.admin')

@section('title', 'خلاصة الحضور والخصومات الشهرية')

@section('content')
<section class="content text-right" dir="rtl" style="padding-top: 20px;">
    <div class="container-fluid">
        
      

        <div class="row no-print">
            <div class="col-md-4">
                <div class="small-box bg-navy shadow-sm">
                    <div class="inner">
                        <h3>{{ $month }}</h3>
                        <p>تقرير شهر</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-danger shadow-sm">
                    <div class="inner">
                        @php
                            $totalMinutes = $summary->sum('total_late');
                            $totalHours = floor($totalMinutes / 60);
                            $remainingMinutes = $totalMinutes % 60;
                        @endphp
                        <h3>{{ $totalHours }} ساعة و {{ $remainingMinutes }} دقيقة</h3>
                        <p>إجمالي تأخيرات المنشأة</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light shadow-sm border-danger">
                    <div class="card-body text-center p-3">
                        <p class="mb-2 font-weight-bold">اعتماد الخصومات وترحيلها</p>
                        <form action="{{ route('admin.attendances.transfer_deductions') }}" method="POST" id="transferForm">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="button" onclick="confirmTransfer()" class="btn btn-danger btn-block shadow-sm">
                                <i class="fas fa-file-export ml-1"></i> ترحيل الكل لقائمة العقوبات
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  <div class="card mb-3 no-print shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.attendances.monthly_summary') }}" method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <label>اختر الشهر والسنة للمراجعة:</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month', $month) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-block">
                            <i class="fas fa-search"></i> عرض التقرير
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-outline card-primary shadow-sm" id="printSection">
            <div class="card-header bg-white border-bottom-0">
                <h3 class="card-title float-left">
                    <strong><i class="fas fa-file-invoice-dollar ml-1"></i> كشف الخصومات المالية المقترحة - {{ $month }}</strong>
                </h3>
                <div class="card-tools float-right no-print">
                   <button onclick="printInNewWindow()" class="btn btn-primary shadow-sm">
                        <i class="fas fa-print"></i> طباعة الكشف للمراجعة
                    </button>
                </div>
            </div>
            
            <div class="alert alert-warning border-right border-warning m-3 no-print d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle ml-2"></i> 
                <strong>تنبيه:</strong> المبالغ أدناه محسوبة بناءً على (الراتب الأساسي ÷ 30 يوم). الضغط على "ترحيل" سيقوم بخصمها رسمياً.
            </div>

            <div class="card-body p-0" id="tableData">
                <table class="table table-hover table-bordered text-center mb-0" id="summaryTable" style="width: 100%; border: 1px solid #dee2e6;">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>#</th>
                            <th style="width: 20%;">اسم الموظف</th>
                            <th>تفاصيل الوقت</th>
                            <th>حسبة الخصم (تقديري)</th>
                            <th class="bg-danger text-white" style="width: 15%;">إجمالي مبلغ الخصم</th>
                            <th class="no-print">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary as $row)
                        @php
                            $salary = $row['basic_salary'] ?? 0;
                            $dailyRate = $salary / 30;
                            $minuteRate = ($dailyRate / 8) / 60;
                            $lateDeduction = $row['total_late'] * $minuteRate;
                            $absentDeduction = $row['absent_days'] * $dailyRate;
                            $totalProposed = $lateDeduction + $absentDeduction;

                            $rowHours = floor($row['total_late'] / 60);
                            $rowMins = $row['total_late'] % 60;
                        @endphp
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="text-right align-middle">
                                <strong>{{ $row['name'] }}</strong>
                                <div class="small text-muted">الراتب: {{ number_format($salary, 2) }}</div>
                            </td>
                            <td class="align-middle text-muted small">
                                <div class="text-danger font-weight-bold">تأخير: {{ $rowHours }}س و {{ $rowMins }}د</div>
                                <div>غياب: {{ $row['absent_days'] }} يوم</div>
                            </td>
                            <td class="align-middle text-left small">
                                <div>خصم تأخير: {{ number_format($lateDeduction, 2) }}</div>
                                <div>خصم غياب: {{ number_format($absentDeduction, 2) }}</div>
                            </td>
                            <td class="align-middle">
                                <span class="h5 font-weight-bold text-danger">{{ number_format($totalProposed, 2) }}</span>
                                <small>د.ل</small>
                            </td>
                          <td class="text-center">
    @php
        // الآن $row['id'] سيعمل بدون أخطاء
        $isTransferred = \App\Models\Penalty::where('employee_id', $row['id'])
                            ->where('description', 'like', "%" . $month . "%")
                            ->exists();
    @endphp

    @if($isTransferred)
        <span class="badge bg-success text-white">
            <i class="fas fa-check-circle"></i> تم الترحيل
        </span>
    @else
        <span class="badge bg-warning text-dark">
            <i class="fas fa-clock"></i> قيد الانتظار
        </span>
    @endif
</td>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top-0 mt-3">
                <div class="row">
                    <div class="col-md-6 text-right">
                        <p class="font-weight-bold mb-0 text-danger" style="font-size: 1.2rem;">
                            إجمالي الخصومات المقترحة للمنشأة: 
                            <span id="grandTotal">{{ number_format($summary->sum(function($r){ 
                                $dr = ($r['basic_salary'] ?? 0)/30; 
                                return ($r['total_late'] * (($dr/8)/60)) + ($r['absent_days'] * $dr); 
                            }), 2) }}</span> د.ل
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// وظيفة الطباعة في نافذة جديدة لحل مشكلة الصفحة البيضاء
function printInNewWindow() {
    const tableHtml = document.getElementById('tableData').innerHTML;
    const grandTotal = document.getElementById('grandTotal').innerText;
    const month = "{{ $month }}";
    
    const printWindow = window.open('', '', 'height=800,width=1000');
    
    printWindow.document.write('<html><head><title>كشف الخصومات - ' + month + '</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { direction: rtl; font-family: DejaVu Sans, sans-serif; padding: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: center; }');
    printWindow.document.write('th, td { border: 1px solid #333; padding: 10px; }');
    printWindow.document.write('th { background-color: #f2f2f2; }');
    printWindow.document.write('.text-danger { color: #d9534f; font-weight: bold; }');
    printWindow.document.write('.no-print { display: none !important; }');
    printWindow.document.write('.header { text-align: center; margin-bottom: 30px; }');
    printWindow.document.write('</style></head><body>');
    
    printWindow.document.write('<div class="header"><h2>كشف الخصومات المالية المقترحة</h2><h3>شهر: ' + month + '</h3></div>');
    printWindow.document.write(tableHtml);
    printWindow.document.write('<div style="margin-top:20px; font-size:1.4rem;"><strong>إجمالي الخصومات المقترحة: </strong><span class="text-danger">' + grandTotal + ' د.ل</span></div>');
    printWindow.document.write('<div style="margin-top:50px; text-align:left;">توقيع الإدارة: ..........................</div>');
    
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 500);
}

function confirmTransfer() {
    Swal.fire({
        title: 'اعتماد الترحيل المالي؟',
        text: "سيتم إنشاء عقوبات مالية لجميع الموظفين وخصمها من حساباتهم وإرسال إشعارات لهم فوراً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، رحّل الخصومات',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري المعالجة...',
                didOpen: () => { Swal.showLoading() }
            });
            document.getElementById('transferForm').submit();
        }
    })
}
</script>
@endpush
@endsection