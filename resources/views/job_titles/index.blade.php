@extends('layouts.admin')

@section('title', 'قائمة المسميات الوظيفية')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">المسميات الوظيفية 👔</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="icon fas fa-check"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    <div class="card shadow-sm border-top-primary">
        <div class="card-header bg-white d-flex align-items-center">
            <h3 class="card-title flex-grow-1 font-weight-bold">قائمة المسميات المتاحة</h3>
            <div class="card-tools">
                <a href="{{ route('job-titles.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> إضافة مسمى جديد
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px" class="text-center">#</th>
                        <th>المسمى الوظيفي</th>
                        <th>القسم التابع له</th> <th>الراتب الأدنى</th>
                        <th>الراتب الأعلى</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobTitles as $title)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><strong>{{ $title->name }}</strong></td>
                            
                            <td>
                                <span class="badge badge-light border px-2">
                                    {{ $title->department->name ?? 'غير محدد' }}
                                </span>
                            </td>

                            <td>{{ number_format($title->min_salary, 2) ?? '---' }}</td>
                            <td>{{ number_format($title->max_salary, 2) ?? '---' }}</td>
                            
                            <td class="text-center">
                                @if($title->status)
                                    <span class="badge badge-success px-2 py-1">نشط</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">متوقف</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap: 15px;">
                                    <a href="{{ route('job-titles.edit', $title->id) }}" class="btn btn-sm btn-info shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('job-titles.destroy', $title->id) }}" method="POST" id="del-job-{{ $title->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger shadow-sm" onclick="confirmJobDelete({{ $title->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">لا توجد بيانات حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmJobDelete(id) {
        Swal.fire({
            title: 'هل تريد حذف هذا المسمى؟',
            text: "تأكد أنه غير مرتبط بموظفين حاليين!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذفه',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('del-job-' + id).submit();
            }
        });
    }
</script>
@endsection