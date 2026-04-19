<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إيصال صرف راتب - {{ $payroll->employee->full_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #eee;
            padding: 20px;
        }

        .invoice-box {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        /* تنسيق الهيدر الجديد */
        .header-section {
            border-bottom: 3px solid #007bff;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }

        .payroll-title {
            color: #007bff;
            font-weight: bold;
            font-size: 22px;
            margin: 0;
        }

        .receipt-label {
            border: 1px solid #007bff;
            color: #007bff;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        /* تنسيق جدول البيانات الشخصية */
        .info-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .info-label {
            color: #666;
            font-weight: bold;
            width: 30%;
        }

        .info-value {
            color: #333;
            font-weight: 700;
            text-align: justify;
        }

        .main-table th {
            background-color: #007bff !important;
            color: white !important;
            text-align: center;
        }

        .total-row {
            background-color: #f1f8ff !important;
            font-weight: bold;
        }

        .signature-box {
            border-top: 1px solid #333;
            width: 150px;
            margin: 10px auto;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .invoice-box {
                border: none;
                box-shadow: none;
                width: 100%;
            }

            .no-print {
                display: none;
            }

            .header-section {
                border-bottom-color: #007bff !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-4">
                    <img src="https://scontent.fmji4-2.fna.fbcdn.net/v/t39.30808-6/535458773_1182094077288007_6356981769473909481_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=1d70fc&_nc_ohc=fW7Ggfn7XsgQ7kNvwGs1af4&_nc_oc=AdpDbJr-M9SdZaisgRJvj8U3Q-CsL09as15icWAXgRpXen1__h0pFn8pcQX_bfz5ERM&_nc_zt=23&_nc_ht=scontent.fmji4-2.fna&_nc_gid=vOkolxhGt0_J7YunoD6IFw&_nc_ss=7a3a8&oh=00_Af3c31h1xwnPA8awyh6Vi5gYEq1AU6wp1QXh-VA6o0ISrw&oe=69D959FF" 

         alt="Logo" 
         class="brand-image img-circle elevation-3" 
         style="opacity: .8; width: 55px; height: 55px; object-fit: cover;">
                    <span class="ml-2 font-weight-bold" style="color: #007bff; font-size: 18px;">شركة دار المرح</span>
                </div>
                <div class="col-4 text-center">
                    <h2 class="payroll-title">إيصال صرف مرتب</h2>
                    <div class="text-muted">شهر {{ $payroll->month }} / {{ $payroll->year }}</div>
                </div>
                <div class="col-4 text-left">
                    <div class="receipt-label">
                        رقم: #{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="info-container">
            <div class="row">
                <div class="col-6 border-left">
                    <table class="w-100">
                        <tr>
                            <td class="info-label">اسم الموظف</td>
                            <td class="info-value">: {{ $payroll->employee->full_name }}</td>
                        </tr>
                        
                        <tr>
                            <td class="info-label">القسم</td>
                            <td class="info-value">: {{ $payroll->employee->department->name ?? '---' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 pr-4">
                    <table class="w-100">
                        <tr>
                            <td class="info-label">تاريخ الصرف</td>
                            <td class="info-value">: {{ $payroll->payment_date }}</td>
                        </tr>
                        <tr>
    <td class="info-label">طريقة الدفع</td>
   <td class="info-value">: 
    @if($payroll->payment_method == 'transfer' || $payroll->payment_method == 'bank_transfer')
        تحويل مصرفي
    @else
        نقدي
    @endif
</td>
</tr>
                    </table>
                </div>
            </div>
        </div>

        <table class="table table-bordered main-table">
            <thead>
                <tr>
                    <th>البند المالي</th>
                    <th>المبلغ (د.ل)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right">الراتب الأساسي</td>
                    <td class="text-center">{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                <tr class="text-success">
                    <td class="text-right">إجمالي المكافآت (+)</td>
                    <td class="text-center">{{ number_format($payroll->total_bonuses, 2) }}</td>
                </tr>
                <tr class="text-danger">
                    <td class="text-right">إجمالي الخصومات (-)</td>
                    <td class="text-center">{{ number_format($payroll->total_deductions, 2) }}</td>
                </tr>
                <tr class="text-danger">
                    <td class="text-right">أقساط السلف (-)</td>
                    <td class="text-center">{{ number_format($payroll->loan_installment, 2) }}</td>
                </tr>
                <tr class="text-danger">
                    <td class="text-right">العهد المحتجزة (-)</td>
                    <td class="text-center">{{ number_format($payroll->held_assets, 2) }}</td>
                </tr>
                <tr class="total-row text-primary">
                    <td class="text-right" style="font-size: 1.2rem;">الصافي المستلم</td>
                    <td class="text-center" style="font-size: 1.2rem;">{{ number_format($payroll->net_salary, 2) }} د.ل
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-section mt-5">
            <div class="row text-center">
                <div class="col-4">
                    <p class="mb-4">توقيع الموظف</p>
                    <div class="signature-box"></div>
                </div>
                <div class="col-4">
                    <p class="mb-4">المحاسب</p>
                    <div class="signature-box"></div>
                </div>
                <div class="col-4">
                    <p class="mb-4">ختم الشركة</p>
                    <div class="mx-auto" style="height: 70px; width: 100px; border: 2px dashed #ddd;"></div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-lg px-5">طباعة الإيصال</button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-lg">إغلاق</button>
        </div>
    </div>

</body>

</html>