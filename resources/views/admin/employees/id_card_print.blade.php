<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; display: flex; justify-content: center; background: #f0f2f5; padding: 10px; }
        
        .id-card {
            width: 350px;
            height: 600px;
            background: #fff;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* الجزء العلوي - تم تقليل الحشو العلوي لرفع النصوص */
        .card-top {
            background: #007bff;
            height: 190px;
            color: white;
            text-align: center;
            padding-top: 15px; /* تم تقليله لرفع "شركة دار المرح" */
            position: relative;
        }
        .card-top h2 { margin: 0; font-size: 24px; font-weight: 700; }
        .card-top .sub-title { 
            margin: 2px 0; 
            font-size: 15px; 
            font-weight: 400;
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 2px 15px;
            border-radius: 10px;
        }

        /* منحنى أبيض */
        .card-top::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: white;
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        /* الصورة الشخصية */
        .profile-container {
            width: 130px;
            height: 130px;
            margin: -75px auto 0;
            position: relative;
            z-index: 10;
        }
        .profile-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* منطقة البيانات */
        .card-body {
            flex-grow: 1;
            padding: 5px 30px;
            text-align: center;
        }
        .emp-name { font-size: 22px; font-weight: 700; color: #333; margin: 10px 0 2px; }
        .emp-job { color: #007bff; font-weight: 600; font-size: 16px; margin-bottom: 20px; }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #f8f9fa;
        }
        .info-row .label { color: #888; font-size: 13px; font-weight: 600; }
        .info-row .value { color: #333; font-size: 14px; font-weight: 700; }
        .info-row i { color: #007bff; margin-left: 8px; }

        /* الجزء السفلي */
        .card-footer {
            height: 100px;
            background: #fafafa;
            border-top: 1px dashed #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
        }
        .qr-img img { width: 65px; height: 65px; }
        .expiry-info { font-size: 10px; color: #aaa; text-align: right; line-height: 1.5; }

        @media print {
            body { background: none; padding: 0; }
            .id-card { box-shadow: none; border: 1px solid #ddd; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="id-card">
    <div class="card-top">
        <h2>شركة دار المرح</h2>
        <div class="sub-title">بطاقة تعريف موظف - {{ $employee->department->name ?? 'العامة' }}</div>
    </div>

    <div class="profile-container">
        <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="صورة الموظف">
    </div>

    <div class="card-body">
        <div class="emp-name">{{ $employee->full_name }}</div>
        <div class="emp-job">{{ $employee->jobTitle->name ?? 'موظف' }}</div>

        <div class="info-row">
            <span class="label"><i class="fas fa-id-badge"></i> كود الموظف:</span>
            <span class="value">{{ $employee->employee_code }}</span>
        </div>

        <div class="info-row">
            <span class="label"><i class="fas fa-map-marker-alt"></i> الفرع:</span>
            <span class="value">{{ $employee->branch->name ?? 'منتزه المرح العائلي' }}</span>
        </div>

        <div class="info-row">
            <span class="label"><i class="fas fa-phone-alt"></i> رقم الهاتف:</span>
            <span class="value" dir="ltr">{{ $employee->phone }}</span>
        </div>
    </div>

    <div class="card-footer">
        <div class="expiry-info">
            تاريخ الإصدار: {{ date('Y-m-d') }}<br>
            صلاحية البطاقة: سنتين
        </div>
        <div class="qr-img">
            {!! QrCode::size(65)->generate($employee->employee_code) !!}
        </div>
    </div>
</div>

</body>
</html>