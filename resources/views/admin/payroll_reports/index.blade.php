@extends('layouts.admin')

@section('title', 'تقرير حركة المرتبات الإداري')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0"><i class="fas fa-chart-line text-primary"></i> تقرير حركة المرتبات الإداري</h1>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="row no-print mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm">
            <div class="inner">
                <h3>{{ number_format($summary['total_basic'] + $summary['total_bonuses'], 2) }}</h3>
                <p>إجمالي المستحقات</p>
            </div>
            <div class="icon"><i class="fas fa-coins"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm">
            <div class="inner">
                <h3>{{ number_format($summary['total_loans'] + $summary['total_deductions'] + $summary['total_assets'], 2) }}</h3>
                <p>إجمالي الاستقطاعات</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm">
            <div class="inner">
                <h3>{{ number_format($summary['total_net'], 2) }}</h3>
                <p>الصافي المطلوب صرفه</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary shadow-sm">
            <div class="inner">
                <h3>{{ $summary['employee_count'] }}</h3>
                <p>عدد الموظفين</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header no-print">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
            <h3 class="card-title font-weight-bold"><i class="fas fa-filter text-muted"></i> فلاتر البحث</h3>
            <a href="{{ route('admin.payroll.reports.print', ['month' => $month, 'year' => $year]) }}" 
   target="_blank" 
   class="btn btn-dark shadow-sm">
    <i class="fas fa-print"></i> طباعة التقرير  
</a>
        </div>

        <form action="{{ route('payroll.reports.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label>البحث عن موظف:</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ادخل اسم الموظف..." value="{{ request('search') }}">
                        <div class="input-group-append"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <label>الشهر:</label>
                    <select name="month" class="form-control select2">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>شهر ({{ $m }})</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>السنة:</label>
                    <select name="year" class="form-control">
                        @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-2 pt-md-4 mt-md-2">
                    <button type="submit" class="btn btn-primary px-4">تحديث</button>
                    <a href="{{ route('payroll.reports.index') }}" class="btn btn-outline-secondary px-3">إعادة تعيين</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="d-none d-print-block text-center mb-5">
            <div class="row align-items-center">
                <div class="col-4 text-right">
                    <h5 class="font-weight-bold">شركة: .................</h5>
                    <p class="m-0">تاريخ الاستخراج: {{ date('Y-m-d') }}</p>
                    <p class="m-0">الوقت: {{ date('H:i') }}</p>
                </div>
                <div class="col-4">
                    <h2 class="font-weight-bold border-bottom pb-2">بيان المرتبات الشهري</h2>
                    <h4 class="mt-2 text-muted">الفترة: {{ $month }} / {{ $year }}</h4>
                </div>
                <div class="col-4 text-left">
                    <p class="m-0">المسؤول: {{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle mb-0" id="reportTable">
                <thead class="bg-light text-dark">
                    <tr>
                        <th style="width: 40px">#</th>
                        <th class="text-center">اسم الموظف</th>
                        <th>الراتب الأساسي</th>
                        <th>المكافآت (+)</th>
                        <th>الخصومات (-)</th>
                        <th>السلف (-)</th>
                        <th>العهد (-)</th>
                        <th class="table-primary font-weight-bold">صافي المستحق</th>
                        <th class="d-none d-print-table-cell">توقيع الاستلام</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $report)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-center font-weight-bold">{{ $report->employee->full_name }}</td>
                        <td>{{ number_format($report->basic_salary, 2) }}</td>
                        <td class="text-success font-weight-bold">+{{ number_format($report->total_bonuses, 2) }}</td>
                        <td class="text-danger">-{{ number_format($report->total_deductions, 2) }}</td>
                        <td class="text-danger">-{{ number_format($report->loan_installment, 2) }}</td>
                        <td class="text-danger">-{{ number_format($report->held_assets, 2) }}</td>
                        <td class="table-info font-weight-bold text-dark">
                            {{ number_format($report->net_salary, 2) }}
                        </td>
                        <td class="d-none d-print-table-cell" style="width: 130px; border-bottom: 1.5px solid #000 !important;"></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-5 text-muted">لا توجد بيانات مرتبات معتمدة لهذه الفترة</td></tr>
                    @endforelse
                </tbody>
                @if($reports->count() > 0)
                <tfoot class="bg-dark text-white font-weight-bold">
                    <tr>
                        <td colspan="2" class="text-left">إجمالي المبالغ الكلية:</td>
                        <td>{{ number_format($summary['total_basic'], 2) }}</td>
                        <td class="text-success">+{{ number_format($summary['total_bonuses'], 2) }}</td>
                        <td class="text-warning">-{{ number_format($summary['total_deductions'], 2) }}</td>
                        <td class="text-warning">-{{ number_format($summary['total_loans'], 2) }}</td>
                        <td class="text-warning">-{{ number_format($summary['total_assets'], 2) }}</td>
                        <td class="bg-primary text-white" style="font-size: 1.1rem;">
                            {{ number_format($summary['total_net'], 2) }} د.ل
                        </td>
                        <td class="d-none d-print-table-cell">---</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<div class="row mt-5 pt-4 d-none d-print-flex text-center">
    <div class="col-4">
        <h5 class="font-weight-bold border-bottom d-inline-block pb-1">إعداد المحاسب</h5>
        <br><br><br>____________________
    </div>
    <div class="col-4">
        <h5 class="font-weight-bold border-bottom d-inline-block pb-1">المراجعة والتدقيق</h5>
        <br><br><br>____________________
    </div>
    <div class="col-4">
        <h5 class="font-weight-bold border-bottom d-inline-block pb-1">اعتماد المدير العام</h5>
        <br><br><br>____________________
    </div>
</div>
@stop

@section('css')
<style>
    /* تحسينات العرض على الشاشة */
    .table td, .table th { vertical-align: middle !important; }

    /* تحسينات الطباعة الشاملة */
    @media print {
        /* إخفاء العناصر غير الضرورية */
        .no-print, 
        .main-footer, 
        .btn, 
        .input-group, 
        .select2-container, 
        .main-sidebar, 
        .main-header,
        .content-header .breadcrumb,
        .card-header form { 
            display: none !important; 
        }

        /* إظهار محتوى التقرير فقط وضمان ملء الصفحة */
        .content-wrapper { 
            background: white !important; 
            margin: 0 !important; 
            padding: 0 !important; 
            width: 100% !important;
            display: block !important;
        }

        .container-fluid, .content, .card {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
        }

        /* ضمان ظهور الجدول والبيانات */
        .table-responsive {
            display: block !important;
            width: 100% !important;
            overflow: visible !important;
        }

        table { 
            width: 100% !important; 
            border-collapse: collapse !important;
            table-layout: auto !important;
        }

        .table td, .table th { 
            border: 1px solid #000 !important; 
            color: #000 !important; 
            padding: 8px !important;
            font-size: 11pt !important;
        }

        /* إظهار الترويسة والتواقيع المخفية */
        .d-print-block { display: block !important; }
        .d-print-flex { display: flex !important; }
        .d-print-table-cell { display: table-cell !important; }

        /* إعدادات الصفحة */
        @page { 
            size: A4 landscape; 
            margin: 1cm; 
        }
    }
</style>
@stop