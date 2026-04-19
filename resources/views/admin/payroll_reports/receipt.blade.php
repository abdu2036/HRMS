<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال صرف راتب</title>
    <style>
        body { font-family: 'XITS', sans-serif; padding: 20px; }
        .receipt-box { border: 2px solid #000; padding: 20px; width: 600px; margin: auto; }
        .header { text-align: center; border-bottom: 1px solid #000; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px text-align: right; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-box">
        <div class="header">
            <h2>إيصال صرف مرتب</h2>
            <p>الشهر: {{ $payroll->month }} | السنة: {{ $payroll->year }}</p>
        </div>
        <table>
            <tr><td><b>اسم الموظف:</b></td><td>{{ $payroll->employee->full_name }}</td></tr>
            <tr><td><b>الراتب الأساسي:</b></td><td>{{ number_format($payroll->basic_salary, 2) }} د.ل</td></tr>
            <tr><td><b>إجمالي الإضافات:</b></td><td>+{{ number_format($payroll->total_bonuses, 2) }} د.ل</td></tr>
            <tr><td><b>إجمالي الخصومات والسلف:</b></td><td>-{{ number_format($payroll->total_deductions + $payroll->loan_installment, 2) }} د.ل</td></tr>
            <tr style="background: #eee;"><td><b>الصافي المستلم:</b></td><td><b>{{ number_format($payroll->net_salary, 2) }} د.ل</b></td></tr>
        </table>
        <div class="footer">
            <div>توقيع الموظف: .................</div>
            <div>توقيع المحاسب: .................</div>
        </div>
    </div>
    <br>
    <center class="no-print"><button onclick="window.print()">طباعة</button></center>
</body>
</html>