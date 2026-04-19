<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            direction: rtl; 
            text-align: right; 
            font-size: 12px;
            margin: 20px;
        }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .content { margin-bottom: 20px; line-height: 1.6; }
        
        /* التنسيقات الجديدة التي سألت عنها تضعها هنا 👇 */
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        
        .table th { 
            background-color: #343a40; /* لون خلفية غامق للهيدر */
            color: #ffffff;            /* لون نص أبيض */
            font-size: 13px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table td {
            font-size: 12px;
            color: #333;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* تلوين السطور الزوجية لسهولة القراءة */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer { margin-top: 50px; }
        .signature { width: 45%; display: inline-block; text-align: center; }
        
        /* مكان الـ QR Code */
        .qr-code { text-align: left; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" style="width: 80px; height: auto; margin-bottom: 10px;">
        <h2>{{ $labels['title'] }}</h2>
        <p>#{{ $data['id'] }} | {{ $data['date'] }}</p>
    </div>

    <div class="content">
  <h3>{{ $labels['declare'] }}</h3>
<p>{{ $labels['ack'] }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>{{ $labels['th1'] }}</th>
                <th>{{ $labels['th2'] }}</th>
                <th>{{ $labels['th3'] }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['name'] }}</td>
                <td>{{ $data['type'] }}</td>
                <td>{{ $data['notes'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p><strong>{{ $labels['f1'] }}</strong></p>
            <p style="margin-top: 20px;">__________________</p>
        </div>
        <div class="signature" style="float: left;">
            <p><strong>{{ $labels['f2'] }}</strong></p>
            <p style="margin-top: 20px;">__________________</p>
        </div>
    </div>

    {{-- إذا أردت إضافة الـ QR Code لاحقاً سيكون مكانه هنا --}}
    <div class="qr-code">
        {{-- مكان صورة الكود --}}
    </div>
</body>
</html>