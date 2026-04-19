<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير المرتبات الرسمي - {{ $month }}/{{ $year }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Cairo', sans-serif; 
            color: #333;
        }
        
        /* ورقة الطباعة */
        .print-container {
            background: white;
            max-width: 297mm; /* عرض A4 بالعرض */
            margin: 20px auto;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            position: relative;
        }

        /* الترويسة العلوية */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #343a40;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info h5 { font-weight: 700; color: #2c3e50; margin-bottom: 5px; }
        .report-title h2 { font-weight: 800; color: #1a1a1a; text-transform: uppercase; letter-spacing: 1px; }
        .report-title .badge { font-size: 1rem; padding: 8px 15px; }

        /* الجدول العصري */
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px; }
        .table-modern th { 
            background-color: #343a40 !important; 
            color: white !important; 
            font-weight: 600;
            padding: 12px;
            border: none;
        }
        .table-modern td { 
            padding: 12px; 
            border-bottom: 1px solid #dee2e6; 
            vertical-align: middle;
        }
        .table-modern tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .table-modern tbody tr:hover { background-color: #f1f1f1; }

        .text-net { color: #28a745; font-weight: 700; font-size: 1.1rem; }
        .text-deduction { color: #dc3545; font-weight: 600; }

        /* منطقة التوقيعات */
        .signature-section { margin-top: 60px; display: flex; justify-content: space-around; }
        .sig-box { border-top: 2px solid #333; width: 200px; padding-top: 10px; font-weight: 700; }

        /* إعدادات الطباعة */
        @media print {
            body { background: white; padding: 0; }
            .print-container { box-shadow: none; margin: 0; width: 100%; max-width: none; padding: 0; }
            .no-print { display: none !important; }
            @page { size: A4 landscape; margin: 1.5cm; }
            .table-modern th { background-color: #343a40 !important; -webkit-print-color-adjust: exact; }
        }

        /* أزرار التحكم العائمة */
        .action-buttons {
            position: fixed;
            bottom: 30px;
            left: 30px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="print-container">
    <div class="report-header">
        <div class="company-info">
            <h5>نظام إدارة الموارد البشرية</h5>
            <p class="text-muted mb-0">تاريخ الاستخراج: {{ date('Y-m-d') }}</p>
            <p class="text-muted mb-0">الوقت: {{ date('H:i') }}</p>
        </div>
        
        <div class="report-title text-center">
            <h2>كشف المرتبات التفصيلي</h2>
            <span class="badge badge-dark shadow-sm">تقرير مرتبات: {{ $month }} / {{ $year }}</span>
        </div>

        <div class="user-info text-left">
            <p class="mb-1 font-weight-bold">المسؤول عن التقرير</p>
            <p class="text-primary mb-0">{{ auth()->user()->name }}</p>
        </div>
    </div>

    <table class="table-modern">
        <thead>
            <tr>
                <th style="border-radius: 8px 0 0 0;">#</th>
                <th class="text-right">اسم الموظف</th>
                <th>الأساسي</th>
                <th>المكافآت (+)</th>
                <th>الخصومات (-)</th>
                <th>السلف (-)</th>
                <th>العهد (-)</th>
                <th style="border-radius: 0 8px 0 0;">صافي المستحق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-right font-weight-bold">{{ $report->employee->full_name }}</td>
                <td>{{ number_format($report->basic_salary, 2) }}</td>
                <td class="text-success">+{{ number_format($report->total_bonuses, 2) }}</td>
                <td class="text-deduction">-{{ number_format($report->total_deductions, 2) }}</td>
                <td class="text-deduction">-{{ number_format($report->loan_installment, 2) }}</td>
                <td class="text-deduction">-{{ number_format($report->held_assets, 2) }}</td>
                <td class="text-net">{{ number_format($report->net_salary, 2) }} د.ل</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: 800;">
                <td colspan="2" class="text-right">الإجمالي الكلي المعتمد:</td>
                <td>{{ number_format($summary['total_basic'], 2) }}</td>
                <td class="text-success">+{{ number_format($summary['total_bonuses'], 2) }}</td>
                <td class="text-danger">-{{ number_format($summary['total_deductions'], 2) }}</td>
                <td class="text-danger">-{{ number_format($summary['total_loans'], 2) }}</td>
                <td class="text-danger">-{{ number_format($summary['total_assets'], 2) }}</td>
                <td class="bg-dark text-white shadow-sm" style="border-radius: 0 0 8px 0;">
                    {{ number_format($summary['total_net'], 2) }} د.ل
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section text-center">
        <div class="sig-box">إعداد المحاسب</div>
        <div class="sig-box">المراجعة الداخلية</div>
        <div class="sig-box">اعتماد المدير العام</div>
    </div>
</div>

<div class="action-buttons no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
        <i class="fas fa-print"></i> تنفيذ الطباعة الآن
    </button>
    <button onclick="window.close()" class="btn btn-outline-danger btn-lg shadow">
        إغلاق المعاينة
    </button>
</div>

</body>
</html>