<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; } /* لدعم العربية في PDF */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>تقرير حضور وانصراف الموظف: {{ $employee->full_name }}</h2>
        <p>الفترة: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>الحضور</th>
                <th>الانصراف</th>
                <th>التأخير (د)</th>
                <th>خروج مبكر (د)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $att)
            <tr>
                <td>{{ $att->date }}</td>
                <td>{{ $att->signin_time }}</td>
                <td>{{ $att->signout_time ?? '--' }}</td>
                <td>{{ $att->late_minutes }}</td>
                <td>{{ $att->early_out_minutes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>إجمالي دقائق التأخير: {{ $totalLate }} دقيقة</p>
        <p>إجمالي دقائق الانصراف المبكر: {{ $totalEarlyOut }} دقيقة</p>
    </div>

    <script>window.print();</script> </body>
</html>