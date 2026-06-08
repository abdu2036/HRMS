@extends('layouts.admin')

@section('title', 'قائمة الموظفين')

@section('content')

    <div class="card shadow-sm">
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-users mr-2 text-primary"></i> قائمة الموظفين المسجلين
            </h3>
            
            <div class="d-flex align-items-center">
                <form action="{{ route('employees.index') }}" method="GET" class="mr-2">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ابحث بالاسم، الكود، أو الهاتف..." 
                               value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-user-plus"></i> إضافة موظف جديد
                </a>
            </div>
        </div>
    </div>
    
    
</div>
        <div class="card-body">
            <table class="table table-hover table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>كود الموظف</th>
                        <th>الاسم الكامل</th>
                        <th>الفرع</th> {{-- العمود الجديد --}}
                        <th>القسم / الوظيفة</th>
                        <th>الشفت</th>
                        <th>الحالة</th>
                        <th>الراتب الأساسي</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $emp->profile_photo ? asset('storage/' . $emp->profile_photo) : asset('assets/img/default.png') }}"
                                    class="img-circle shadow-sm border" style="width: 45px; height: 45px; object-fit: cover;">
                            </td>
                            <td><span class="badge badge-secondary px-2 py-1">{{ $emp->employee_code }}</span></td>
                            <td class="text-right">
                                <strong>{{ $emp->full_name }}</strong><br>
                                <small class="text-muted"><i class="fas fa-phone-alt fa-xs"></i> {{ $emp->phone }}</small>
                            </td>
                            <td>
                                {{-- عرض اسم الفرع --}}
                                <span class="text-primary"><i class="fas fa-map-marker-alt"></i>
                                    {{ $emp->branch->name ?? 'غير محدد' }}</span>
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ $emp->department->name ?? 'غير محدد' }}</span><br>
                                <small class="badge badge-light border">{{ $emp->jobTitle->name ?? '' }}</small>
                            </td>

                            <td>
                                <span
                                    class="badge badge-info-border text-info border-info px-2">{{ $emp->shift->name ?? '-' }}</span>
                            </td>
                            <td>
                                @if($emp->status == 'active' || $emp->status == 1)
                                    <span class="badge badge-success shadow-sm">يعمل</span>
                                @else
                                    <span class="badge badge-danger shadow-sm">لايعمل </span>
                                @endif
                            </td>
                            <td>
                                {{-- نستخدم $emp->salary لأن هذا هو اسم العلاقة المعرف في الموديل لديك --}}
                                @if(isset($emp->salary) && $emp->salary->basic_salary > 0)
                                    {{ number_format($emp->salary->basic_salary, 2) }} د.ل
                                @else
                                    {{-- عرض الراتب الاحتياطي من جدول الموظفين مباشرة --}}
                                    {{ number_format($emp->basic_salary, 2) }} د.ل
                                @endif
                            </td>
                            <td>

                                <div class="btn-sm">
                                    <a href="{{ route('employees.createAccount', $emp->id) }}" class="btn btn-sm btn-success"
                                        title="إنشاء حساب دخول">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                    <a href="{{ route('employees.show', $emp->id) }}" class="btn btn-info btn-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-warning btn-sm"
                                        title="تعديل">
                                        <i class="fas fa-edit text-white"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteEmployee({{ $emp->id }})"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $emp->id }}" action="{{ route('employees.destroy', $emp->id) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
         </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-center">
            {{ $employees->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>

    {{-- سكريبت الحذف كما هو --}}
    <script>
        function deleteEmployee(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف بيانات الموظف وكافة ملفاته نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection

{{-- قسم خاص بلوحة تحكم الموظف --}}