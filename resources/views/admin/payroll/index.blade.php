@extends('layouts.admin')

@section('title', 'إدارة صرف المرتبات')

@section('content_header')
<h1 class="m-0 text-dark">نظام صرف المرتبات - شهر {{ $month }} / {{ $year }}</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title mt-1">كشف رواتب الموظفين</h3>
        
       <div class="card-tools">
    <form action="{{ route('admin.payroll.index') }}" method="GET" class="form-inline">
        <div class="input-group">
            
            <input type="text" name="search" class="form-control" 
                   placeholder="ابحث باسم الموظف..." 
                   value="{{ request('search') }}" 
                   style="width: 350px; height: 40px; font-weight: bold;">

            <div class="input-group-prepend ml-2">
                <span class="input-group-text" style="height: 40px;">الشهر</span>
            </div>
            <input type="number" name="month" class="form-control" 
                   value="{{ request('month', $month) }}" min="1" max="12" 
                   style="width: 100px; height: 40px; text-align: center;">

            <div class="input-group-prepend ml-2">
                <span class="input-group-text" style="height: 40px;">السنة</span>
            </div>
            <input type="number" name="year" class="form-control" 
                   value="{{ request('year', $year) }}" 
                   style="width: 120px; height: 40px; text-align: center;">

            <div class="input-group-append ml-2">
                <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 25px; font-weight: bold;">
                    <i class="fas fa-search"></i> عرض النتائج
                </button>
                
                <a href="{{ route('admin.payroll.index') }}" class="btn btn-default border ml-1" style="height: 40px; display: flex; align-items: center;">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </div>
    </form>
</div>
    </div>
</div>
    
    <div class="card-body p-0">
        <table class="table table-bordered table-striped table-hover text-center mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>الموظف</th>
                    <th>الراتب الأساسي</th>
                    <th>المكافآت (+)</th>
                    <th>الخصومات (-)</th>
                    <th>السلف (قسط)</th>
                    <th>عهدة محجوزة</th>
                    <th>صافي المستحق</th>
                    <th>طريقة الدفع</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
              @foreach($employees as $emp)
    @php
        // القيم تأتي الآن جاهزة من الـ Controller سواء كانت معتمدة أو معلقة
        $current_net = ($emp->basic_salary + $emp->total_bonuses) - ($emp->total_deductions + $emp->loan_installment + $emp->total_custody_to_deduct);
    @endphp
    <tr>
        <td class="align-middle">{{ $loop->iteration }}</td>
        <td class="align-middle text-bold">{{ $emp->full_name }}</td>
        <td class="align-middle basic-val">{{ number_format($emp->basic_salary, 2) }}</td>
        <td class="align-middle text-success bonus-val">+{{ number_format($emp->total_bonuses, 2) }}</td>
        <td class="align-middle text-danger deduct-val">-{{ number_format($emp->total_deductions, 2) }}</td>
        <td class="align-middle text-orange loan-val">-{{ number_format($emp->loan_installment, 2) }}</td>
        
        <td class="align-middle text-center custody-val">
            {{ number_format($emp->total_custody_to_deduct, 2) }}
            @if($emp->total_custody_to_deduct > 0 && !$emp->payroll_status)
                <div class="small text-muted">إجمالي العهد</div>
            @endif
        </td>

        <td class="align-middle">
            <span id="net_display_{{ $emp->id }}" class="badge badge-primary p-2" style="font-size: 1.1rem;">
                {{ number_format($current_net, 2) }}
            </span>
        </td>
        
        {{-- باقي الأعمدة كما هي في كودك الأصلي --}}

                        <td class="align-middle">
    @if(!$emp->payroll_status)
        {{-- أضفنا id للسلكتور وربطناه بالفورم عبر خاصية form --}}
        <select class="form-control form-control-sm" 
        id="select_method_{{ $emp->id }}" 
        required>
    <option value="">اختر طريقة الدفع</option>
    <option value="cash">نقداً</option>
   <option value="bank_transfer">تحويل مصرفي</option>
</select>
    @else
        <span class="badge badge-light">مؤكد</span>
    @endif
</td>

<td class="align-middle">
    @if($emp->payroll_status && $emp->payroll_record)
        <div class="d-flex flex-column align-items-center" style="gap: 5px;">
            <span class="badge badge-success p-2 w-100">
                <i class="fas fa-check-circle"></i> تم الاعتماد
            </span>
            
            {{-- زر إيصال الصرف --}}
            <a href="{{ route('admin.payroll.receipt', $emp->payroll_record->id) }}" 
               target="_blank" 
               class="btn btn-outline-primary btn-xs btn-block shadow-sm mt-1">
                <i class="fas fa-print"></i> إيصال الصرف
            </a>
        </div>
    @else
        <form action="{{ route('admin.payroll.process') }}" method="POST" class="payroll-form" id="form_{{ $emp->id }}">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <input type="hidden" name="held_assets" id="input_held_{{ $emp->id }}" value="{{ $emp->total_custody_to_deduct }}">
            <input type="hidden" name="net_salary" id="input_net_{{ $emp->id }}" value="{{ $current_net }}">

            <input type="hidden" name="payment_method" id="input_method_{{ $emp->id }}" value="">

            <input type="hidden" name="basic_salary" value="{{ $emp->basic_salary }}">
            <input type="hidden" name="total_bonuses" value="{{ $emp->total_bonuses }}">
            <input type="hidden" name="total_deductions" value="{{ $emp->total_deductions }}">
            <input type="hidden" name="loan_installment" value="{{ $emp->loan_installment }}">

            <button type="button" 
                    onclick="confirmPayroll({{ $emp->id }})" 
                    class="btn btn-danger btn-sm btn-block shadow-sm">
                <i class="fas fa-money-check-alt"></i> اعتماد وصرف
            </button>
        </form>
    @endif
</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
    // هذه الدالة ستعمل فور الضغط على الزر مباشرة
    function confirmPayroll(empId) {
        // 1. جلب السلكتور الخاص بطريقة الدفع للموظف المعني
        var methodSelector = $('#select_method_' + empId);
        var methodVal = methodSelector.val();

        // 2. التحقق من اختيار الطريقة
        if (methodVal === "" || methodVal === null || methodVal === "#") {
            alert("⚠️ يرجى اختيار طريقة الدفع أولاً!");
            methodSelector.addClass('is-invalid').focus();
            return false;
        }

        // 3. تحديث الحقل المخفي قبل الإرسال (لضمان الدقة)
        $('#input_method_' + empId).val(methodVal);

        // 4. جلب اسم الموظف والمبلغ للرسالة (اختياري للتدقيق)
        var empName = $('#form_' + empId).closest('tr').find('td:nth-child(2)').text().trim();
        var finalAmount = $('#input_net_' + empId).val();
        var methodText = methodSelector.find('option:selected').text();

        // 5. إظهار مربع التأكيد
        var confirmMsg = "تأكيد صرف راتب: " + empName + "\n" +
                         "بمبلغ: " + finalAmount + " د.ل\n" +
                         "طريقة الدفع: " + methodText + "\n\n" +
                         "هل أنت متأكد؟";

        if (confirm(confirmMsg)) {
            // إرسال الفورم برمجياً
            document.getElementById('form_' + empId).submit();
        }
    }
</script>
@stop