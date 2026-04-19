@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-users ml-2 text-primary"></i> إدارة المستخدمين والصلاحيات
                </h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid" dir="rtl">
        
        {{-- تنبيهات النجاح --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible shadow-sm border-0">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> تم بنجاح!</h5>
                {{ session('success') }}
            </div>
        @endif

        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title float-right font-weight-bold">
                    <i class="fas fa-list ml-2"></i> قائمة المستخدمين المسجلين
                </h3>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-right">
                        <thead class="bg-navy text-white">
                            <tr>
                                <th class="px-4 py-3">الاسم 👤</th>
                                <th class="px-4 py-3">البريد الإلكتروني 📧</th>
                                <th class="px-4 py-3">الدور الحالي 🔑</th>
                                <th class="px-4 py-3">الحالة 🟢</th>
                                <th class="px-4 py-3 text-center">العمليات ⚙️</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="font-weight-bold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-primary px-2 py-1 shadow-sm" style="font-size: 12px;">
                                            <i class="fas fa-shield-alt fa-xs ml-1"></i>
                                            {{ __('permissions.' . $role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="text-success font-weight-bold">
                                        <i class="fas fa-circle fa-xs ml-1"></i> نشط
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           class="btn btn-sm btn-outline-primary mx-1 shadow-sm" 
                                           title="تعديل">
                                            <i class="fas fa-user-edit"></i>
                                        </a>

                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $user->id }})" 
                                                    class="btn btn-sm btn-outline-danger mx-1 shadow-sm" 
                                                    title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .table th, .table td { vertical-align: middle !important; }
        .card-title { float: right !important; }
        .bg-navy { background-color: #001f3f !important; }
        /* لتصحيح اتجاه أيقونات AdminLTE في الوضع العربي */
        .btn-group .btn { border-radius: 4px !important; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف حساب المستخدم نهائياً من النظام!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        })
    }
    </script>
@stop