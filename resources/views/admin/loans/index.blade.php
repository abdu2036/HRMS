@extends('layouts.admin')

@section('title', 'إدارة السلف والقروض')

@section('content')
    <section class="content">
        <div class="container-fluid">

            {{-- صناديق الإحصائيات --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($loans->where('status', 'active')->sum('remaining_amount'), 2) }}</h3>
                            <p>إجمالي السلف النشطة (المعلقة)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title text-bold">سجل السلف والقروض</h3>
                    <div class="card-tools">
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addLoanModal">
                            <i class="fas fa-plus"></i> تسجيل سلفة جديدة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>المبلغ الإجمالي</th>
                                <th>القسط الشهري</th>
                                <th>المبلغ المتبقي</th>
                                <th>تاريخ البدء</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                                <th>إيصال</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="text-bold">{{ $loan->employee->full_name ?? '---' }}</td>
                                    <td>{{ number_format($loan->amount, 2) }}</td>
                                    <td>{{ number_format($loan->installment, 2) }}</td>
                                    <td class="text-danger text-bold">{{ number_format($loan->remaining_amount, 2) }}</td>
                                    <td>{{ $loan->start_date }}</td>
                                    <td>
                                        @if($loan->status == 'pending')
                                            <span class="badge badge-warning text-dark">قيد الانتظار</span>
                                        @elseif($loan->status == 'active')
                                            <span class="badge badge-success">نشطة (مقبولة)</span>
                                        @elseif($loan->status == 'rejected')
                                            <span class="badge badge-danger">مرفوضة</span>
                                        @else
                                            <span class="badge badge-primary">تم السداد</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- أزرار التحكم --}}
                                        @if($loan->status == 'pending')
                                            <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="موافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#rejectModal{{ $loan->id }}" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        {{-- زر التعديل --}}
                                        
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#editLoan{{ $loan->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- زر الحذف المتصل بكود SweetAlert في الماستر --}}
                                      <!--  <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $loan->id }})" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- فورم الحذف المخفي --}}
                                        <form id="delete-form-{{ $loan->id }}"
                                            action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form> -->
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-default border shadow-sm" 
                                                onclick="printLoanReceipt('{{ $loan->employee?->full_name ?? '---' }}', '{{ number_format($loan->amount, 2) }}', '{{ number_format($loan->installment, 2) }}', '{{ $loan->start_date }}')">
                                            <i class="fas fa-print text-primary"></i> إيصال
                                        </button>
                                    </td>
                                </tr>

                                {{-- مودال الرفض --}}
                                <div class="modal fade" id="rejectModal{{ $loan->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">رفض طلب سلفة: {{ $loan->employee->name }}</h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body text-right">
                                                    <label>سبب الرفض (اختياري)</label>
                                                    <textarea name="admin_reply" class="form-control" rows="3"
                                                        placeholder="اكتب سبب الرفض..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- مودال التعديل --}}
                                <div class="modal fade" id="editLoan{{ $loan->id }}" tabindex="-1" role="dialog"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content border-info">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">تعديل بيانات السلفة</h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-right">
                                                    <div class="form-group">
                                                        <label>إجمالي المبلغ</label>
                                                        <input type="number" name="amount" class="form-control"
                                                            value="{{ $loan->amount }}" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>القسط الشهري</label>
                                                        <input type="number" name="installment" class="form-control"
                                                            value="{{ $loan->installment }}" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>تاريخ البدء</label>
                                                        <input type="date" name="start_date" class="form-control"
                                                            value="{{ $loan->start_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-info">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- مودال إضافة سلفة جديدة --}}
    <div class="modal fade" id="addLoanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-bold">تسجيل سلفة موظف جديدة</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.loans.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-right">
                        <div class="form-group">
                            <label>اختر الموظف</label>
                            <select name="employee_id" id="employeeSelect" class="form-control select2" required style="width: 100%;">
                                <option value="">-- اختر الموظف --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }} ({{ $employee->employee_code ?? 'بدون كود' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المبلغ الإجمالي</label>
                                    <input type="number" name="amount" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>القسط الشهري</label>
                                    <input type="number" name="installment" class="form-control" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>تاريخ البدء</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning text-bold">حفظ السلفة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- استدعاء المكاتب بصيغة CDN لضمان تحميلها بشكل سليم إن لم تكن مدمجة بالماستر --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        /* تنسيقات مخصصة لتثبيت اتجاه البحث باللغة العربية والتوافق مع المودال */
        .select2-container {
            z-index: 99999 !important; /* لضمان ظهور القائمة فوق المودال دائماً */
        }
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }
        .select2-search__field {
            direction: rtl !important;
            text-align: right !important;
        }
        .select2-results__option {
            text-align: right !important;
            direction: rtl !important;
        }
    </style>
@endpush

@push('js')
    {{-- تضمين مكتبة Select2 بشكل صريح هنا --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // استخدام window.addEventListener لضمان تنفيذ الكود حتى لو تم تحميل jQuery متأخراً في ملف الماستر
        window.addEventListener('load', function() {
            if (window.jQuery) {
                $(document).ready(function() {
                    // تشغيل السكربت وربطه عند فتح المودال مباشرة لتجنب تجميد أبعاد صندوق الخيارات
                    $('#addLoanModal').on('shown.bs.modal', function () {
                        $('#employeeSelect').select2({
                            theme: 'bootstrap4',
                            placeholder: "-- اختر الموظف --",
                            allowClear: true,
                            dir: "rtl",
                            dropdownParent: $('#addLoanModal') /* لمنع اختفاء صندوق البحث خلف المودال */
                        });
                    });
                });
            } else {
                console.error("jQuery is not loaded yet.");
            }
        });

        function printLoanReceipt(employeeName, totalAmount, monthlyInstallment, startDate) {
            var printWindow = window.open('', '', 'height=700,width=850');
            
            printWindow.document.write('<html><head><title>إيصال صرف سلفة مالية</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: "Cairo", sans-serif; padding: 40px; direction: rtl; text-align: right; background-color: #fff !important; color: #000 !important; }');
            printWindow.document.write('.receipt-box { border: 3px double #000; padding: 25px; margin: 10px auto; max-width: 750px; }');
            printWindow.document.write('.receipt-header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 15px; margin-bottom: 20px; }');
            printWindow.document.write('.receipt-title { font-size: 24px; font-weight: bold; margin-top: 10px; background: #f2f2f2; display: inline-block; padding: 5px 25px; border: 1px solid #000; }');
            printWindow.document.write('.receipt-row { font-size: 16px; line-height: 2.2; margin-bottom: 10px; }');
            printWindow.document.write('.underline-space { border-bottom: 1px dotted #000; display: inline-block; font-weight: bold; padding: 0 10px; text-align: center; }');
            printWindow.document.write('.footer-signatures { margin-top: 50px; border-top: 1px dashed #000; padding-top: 20px; }');
            printWindow.document.write('</style></head><body>');
            
            printWindow.document.write('<div class="receipt-box">');
            
            printWindow.document.write('<div class="receipt-header">');
            printWindow.document.write('<h3 style="font-weight: bold; margin: 0;">نظام إدارة الموظفين والموارد البشرية</h3>');
            printWindow.document.write('<p style="font-size: 14px; color: #444; margin: 5px 0 0 0;">إدارة الشؤون المالية والرواتب</p>');
            printWindow.document.write('<div class="receipt-title">إيصال صرف سلفة مالي</div>');
            printWindow.document.write('</div>');
            
            printWindow.document.write('<div class="receipt-row">');
            printWindow.document.write('تاريخ الصرف: <span class="underline-space" style="width: 150px;">' + startDate + '</span> &nbsp;&nbsp;&nbsp;&nbsp; ');
            printWindow.document.write('المبلغ المالي الصافي: <span class="underline-space" style="width: 180px; font-size: 18px;">' + totalAmount + ' د.ل</span>');
            printWindow.document.write('</div>');
            
            printWindow.document.write('<div class="receipt-row" style="margin-top: 15px;">');
            printWindow.document.write('يصرف للموظف السيد/ة: <span class="underline-space" style="width: 450px; font-size: 17px;">' + employeeName + '</span>');
            printWindow.document.write('</div>');
            
            printWindow.document.write('<div class="receipt-row">');
            printWindow.document.write('وذلك عبارة عن سلفة مالية مستحقة، على أن يتم خصمها من المرتب الشهري على أقساط ثابتة وقيمة القسط: ');
            printWindow.document.write('<span class="underline-space" style="width: 150px;">' + monthlyInstallment + ' د.ل</span> شهرياً ابتكاراً من تاريخ التفعيل المعتمد.');
            printWindow.document.write('</div>');
            
            printWindow.document.write('<div class="receipt-row" style="margin-top: 20px; font-style: italic; font-size: 14px; color: #333;">');
            printWindow.document.write('إقرار واستلام: "أقر أنا الموقع أدناه بأنني استلمت المبلغ المذكور أعلاه نقداً/مصرفياً وأوافق على شروط الخصم الموضحة بالمنظومة."');
            printWindow.document.write('</div>');
            
            printWindow.document.write('<div class="row footer-signatures text-center">');
            printWindow.document.write('<div class="col-4"><h5>توقيع المستلم (الموظف)</h5><br><p>...........................</p></div>');
            printWindow.document.write('<div class="col-4"><h5>المحاسب المالي</h5><br><p>...........................</p></div>');
            printWindow.document.write('<div class="col-4"><h5>اعتماد الإدارة</h5><br><p>...........................</p></div>');
            printWindow.document.write('</div>');
            
            printWindow.document.write('</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            
            setTimeout(function() {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 350);
        }
    </script>
@endpush