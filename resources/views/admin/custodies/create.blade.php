@extends('layouts.admin')

@section('title', 'إضافة عهدة جديدة')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">تسجيل عهدة جديدة 📦</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('custodies.store') }}" method="POST">
                    @csrf

                    {{-- 1. اختيار الموظف (تمت إعادته لضمان اكتمال النموذج) --}}
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">الموظف المسؤول 👤</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">-- اختر الموظف --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. اسم العهدة العام --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">عنوان العهدة 🏷️</label>
                        <input type="text" name="name" class="form-control" placeholder="مثلاً: عهدة الموظف السنوية" required>
                    </div>

                    {{-- 3. نوع العهدة --}}
                    <div class="mb-3">
                        <label for="type_selector" class="form-label">نوع العهدة ⚙️</label>
                        <select name="type" id="type_selector" class="form-control" required>
                            <option value="">-- اختر النوع --</option>
                            <option value="hardware">جهاز / عهدة عينية</option>
                            <option value="financial">عهدة مالية</option>
                            <option value="both">كلاهما (جهاز + مالية)</option>
                        </select>
                    </div>

                    {{-- 4. حقل الجهاز (يظهر ديناميكياً) --}}
                    <div id="hardware_field" class="mb-3" style="display: none;">
                        <label class="form-label text-primary">تفاصيل الجهاز (الموديل/الرقم التسلسلي) 💻</label>
                        <input type="text" name="hardware_details" id="hardware_input" class="form-control" placeholder="مثلاً: لابتوب Dell G15 - SN: 12345">
                    </div>

                    {{-- 5. حقل القيمة المالية (يظهر ديناميكياً) --}}
                    <div id="financial_field" class="mb-3" style="display: none;">
                        <label class="form-label text-success">القيمة المالية 💰</label>
                        <input type="number" name="amount" id="amount_input" class="form-control" value="0" step="0.01">
                    </div>

                    {{-- 6. حقل الملاحظات --}}
                    <div class="mb-3">
                        <label class="form-label">ملاحظات إضافية 📝</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="أي تفاصيل أخرى..."></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-5 font-weight-bold">حفظ وتسليم العهدة</button>
                        <a href="{{ route('custodies.index') }}" class="btn btn-secondary px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelector = document.getElementById('type_selector');
        const hardwareField = document.getElementById('hardware_field');
        const financialField = document.getElementById('financial_field');

        typeSelector.addEventListener('change', function () {
            const selectedValue = this.value;

            // إخفاء الحقول في البداية
            hardwareField.style.display = 'none';
            financialField.style.display = 'none';

            // إظهار الحقول بناءً على الاختيار
            if (selectedValue === 'hardware') {
                hardwareField.style.display = 'block';
            } else if (selectedValue === 'financial') {
                financialField.style.display = 'block';
            } else if (selectedValue === 'both') {
                hardwareField.style.display = 'block';
                financialField.style.display = 'block';
            }
        });
    });
</script>
@endpush