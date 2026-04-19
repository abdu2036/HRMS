@extends('layouts.admin')

{{-- تحديد العناوين الديناميكية --}}
@section('title', 'إدارة الفروع')
@section('content_header', 'إدارة الفروع والشركات')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">إدارة الفروع</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- عرض رسائل النجاح باستخدام SweetAlert --}}
            @if(session('success'))
                <script>
                    Swal.fire({
                        title: 'تمت العملية بنجاح!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        confirmButtonText: 'حسناً',
                        timer: 3000
                    });
                </script>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-dark">
                    <h3 class="card-title">قائمة الفروع المسجلة 🏢</h3>
                    <div class="card-tools">
                        <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> إضافة فرع جديد
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px">#</th>
                                    <th>اسم الفرع</th>
                                    <th>الموقع (GPS)</th> {{-- العمود الجديد --}}
                                    <th>نطاق البصمة</th> {{-- العمود الجديد --}}
                                    <th>رقم الهاتف</th>
                                    <th>الشركة الأم</th>
                                    <th style="width: 150px">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $branch->name }}</strong><br>
                                            <small class="text-muted">{{ $branch->address ?? 'بدون عنوان' }}</small>
                                        </td>
                                        <td>
                                            @if($branch->latitude && $branch->longitude)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-map-marker-alt"></i> محدد
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times-circle"></i> غير محدد
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-primary font-weight-bold">{{ $branch->radius_meters ?? 100 }} متر</span>
                                        </td>
                                        <td>{{ $branch->phone ?? '---' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $branch->company->company_name ?? 'غير مرتبط' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center" style="gap: 5px;">
                                                {{-- زر التعديل --}}
                                                <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- زر الحذف --}}
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $branch->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" id="delete-form-{{ $branch->id }}" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted">لا توجد فروع مضافة حالياً</p>
                                            <a href="{{ route('branches.create') }}" class="btn btn-outline-primary btn-sm">أضف أول فرع الآن</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- وضعنا السكريبت خارج الـ loop ليكون الكود أنظف --}}
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف كافة البيانات المرتبطة بهذا الفرع!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection