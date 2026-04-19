@extends('layouts.admin')

@section('title', 'إدارة الأقسام')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">إدارة الأقسام الإدارية 🏢</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-left">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"> الرئيسية</a></li>
                    <li class="breadcrumb-item active">إدارة الأقسام</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow-sm border-top-primary">
            <div class="card-header bg-white d-flex align-items-center">
                <h3 class="card-title flex-grow-1 font-weight-bold">قائمة الهيكل التنظيمي</h3>
                <div class="card-tools">
                    <a href="{{ route('departments.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus"></i> إضافة قسم جديد
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px" class="text-center">الترتيب</th>
                            <th>كود القسم</th>
                            <th>اسم القسم</th>
                            <th>يتبع لـ</th>
                            <th>مدير القسم</th> {{-- العمود الجديد --}}
                            <th class="text-center">الحالة</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                            <tr>
                                <td class="text-center">{{ $dept->order ?? '-' }}</td>
                                <td><span class="badge badge-secondary px-2 py-1">{{ $dept->code }}</span></td>
                                <td><strong>{{ $dept->name }}</strong></td>
                                <td>
                                    @if($dept->parent)
                                        <span class="text-info"><i class="fas fa-level-up-alt fa-rotate-90"></i>
                                            {{ $dept->parent->name }}</span>
                                    @else
                                        <span class="badge badge-light border">قسم رئيسي</span>
                                    @endif
                                </td>
                                
                                {{-- تم وضع السيلكت داخل td لضبط التصميم --}}
                       <td>
    @if($dept->manager)
        <span class="badge badge-info p-2">
            <i class="fas fa-user-tie mr-1"></i> {{ $dept->manager->full_name }}
        </span>
    @else
        <span class="text-muted small">غير محدد</span>
    @endif
</td>
                                <td class="text-center">
                                    @if($dept->status)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">متوقف</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{-- استخدام d-flex و gap للتباعد بين الأزرار --}}
                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                        <a href="{{ route('departments.edit', $dept->id) }}"
                                            class="btn btn-sm btn-info shadow-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('departments.destroy', $dept->id) }}" method="POST"
                                            id="delete-form-{{ $dept->id }}" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger shadow-sm"
                                                onclick="confirmDelete({{ $dept->id }})" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-gray"></i>
                                    <p>لا توجد أقسام مضافة حالياً.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <small class="text-muted">إجمالي الأقسام: {{ $departments->count() }}</small>
            </div>
        </div>
    </div>
@endsection