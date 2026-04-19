<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>مراسلة رسمية - {{ $correspondence->reference_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* إعدادات الصفحة الأساسية للطباعة */
        @page {
            size: A4;
            margin: 0; /* نلغي هوامش المتصفح تماماً */
        }

        body {
            font-family: 'Amiri', serif;
            line-height: 1.6; /* قللنا التباعد قليلاً لتوفير مساحة */
            color: #222;
            margin: 0;
            padding: 0;
            background: #eee; /* لون خلفية للمعاينة فقط */
            -webkit-print-color-adjust: exact; /* لضمان طباعة الألوان والخلفيات */
        }
        
        /* الحاوية الرئيسية المضبوطة لحجم A4 */
        .page {
            width: 210mm; /* عرض A4 دقيق */
            height: 296mm; /* طول A4 دقيق (أقل بـ 1 ملم تجنباً لمشاكل المتصفح) */
            padding: 15mm 20mm; /* هوامش داخلية مناسبة */
            margin: 10mm auto; /* توسيط في المعاينة على الشاشة */
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box; /* لضمان حساب البادينج ضمن العرض الكلي */
            overflow: hidden; /* لمنع أي محتوى من الخروج خارج الصفحة */
        }

        /* خط أزرق علوي نحيف للاحترافية */
        .page::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4mm;
            background-color: #1a5a96;
        }

        /* الهيدر المحسن */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #1a5a96; /* خط أنحف */
            padding-bottom: 5mm;
            margin-bottom: 8mm;
            margin-top: 5mm; /* مساحة بعد الخط الأزرق */
        }
        .company-name { font-size: 20px; font-weight: bold; color: #1a5a96; line-height: 1.2; }
        .logo-img { max-width: 100px; height: auto; } /* صغرنا الشعار قليلاً */
        .meta-info { font-size: 13px; text-align: left; line-height: 1.4; }

        /* عنوان المراسلة */
        .doc-title { text-align: center; margin: 15mm 0; }
        .doc-title h1 {
            display: inline-block;
            padding: 6px 30px;
            border: 1.5px solid #1a5a96;
            border-radius: 50px;
            font-size: 18px;
            background: #f8f9fa;
            color: #1a5a96;
            margin: 0;
        }

        /* التفاصيل */
        .addressing { margin-bottom: 20mm; font-size: 17px; }
        .subject-line { font-weight: bold; text-decoration: underline; margin-bottom: 20mm; font-size: 17px; }
        
        /* محتوى الرسالة - قمنا بضبط المساحة */
        .content-body {
            min-height: 120mm; /* مساحة مضمونة للمحتوى */
            font-size: 16px;
            text-align: justify;
            padding: 0 5mm;
            margin-bottom: 10mm;
        }

        /* التواقيع */
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: auto; /* تدفع التواقيع للأسفل إذا كان المحتوى قصيراً */
            padding-bottom: 20mm; /* مساحة قبل الفوتر */
        }
        .sig-box { text-align: center; width: 180px; font-size: 15px; }
        .sig-line { margin-top: 30px; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; }

        /* فوتر الصفحة السفلي الثابت */
        .footer-bottom {
            position: absolute;
            bottom: 5mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 3mm;
        }

        /* إعدادات خاصة بالطباعة الفعلية */
        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page {
                margin: 0; /* نلغي الهامش الخارجي عند الطباعة */
                box-shadow: none;
                width: 210mm;
                height: 297mm; /* الطول الكامل عند الطباعة */
                border: none;
            }
            .page::before { -webkit-print-color-adjust: exact; } /* لضمان طباعة الخط الأزرق */
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; padding: 15px; background: #343a40; position: fixed; top: 0; left: 0; right: 0; z-index: 999;">
    <button onclick="window.print()" style="padding: 10px 25px; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 15px; font-weight: bold;">
        <i class="fas fa-print"></i> تأكيد أمر الطباعة (A4)
    </button>
</div>

<div class="page">
    <div class="header-section">
        <div class="company-name">
            شركة دار المرح<br>
            <small style="font-size: 12px; color: #555;">للخدمات الترفيهية والإدارية</small>
        </div>
        <div>
            <img src="{{ asset('dist/img/logo.png') }}" class="logo-img" alt="Logo">
        </div>
        <div class="meta-info">
            <strong>الرقم الإشاري:</strong> {{ $correspondence->reference_number }}<br>
            <strong>التاريخ:</strong> {{ $correspondence->created_at->format('Y/m/d') }}<br>
            <strong>المرفقات:</strong> يوجد
        </div>
    </div>

    <div class="doc-title">
        <h1>مراسلة رسمية إدارية</h1>
    </div>

    <div class="addressing">
        إلى السيد المحترم: <strong>مدير قسم {{ $correspondence->department->name ?? '........' }}</strong>
    </div>

    <div class="subject-line">
        الموضوع: {{ $correspondence->subject }}
    </div>

    <div class="content-body">
        {!! $correspondence->content !!}
    </div>

    <div class="signature-area">
        <div class="sig-box">
            <p>ختم القسم المستلم</p>
            <div style="height: 60px; border: 1px dashed #ccc; margin: 8px 0;"></div>
        </div>
        <div class="sig-box">
            <p>توقيع المدير العام</p>
            <div class="sig-line">{{ $correspondence->sender->name }}</div>
        </div>
    </div>

    <div class="footer-bottom">
        ليبيا - طرابلس - شبكة منتزه المرح العائلي | هاتف: 0910000000 | بريد إلكتروني: info@funpark.ly
    </div>
</div>

</body>
</html>