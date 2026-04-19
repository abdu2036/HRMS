@extends('layouts.admin')

@section('title', 'إدارة الأدوار')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">إدارة الأدوار والصلاحيات</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary shadow-sm text-right">
        <div class="card-header border-0">
            <h3 class="card-title float-right">
                <i class="fas fa-shield-alt ml-1"></i> قائمة الأدوار المسجلة
            </h3>
            {{-- زر الإضافة هنا --}}
            <div class="card-tools float-left">
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-plus ml-1"></i> إضافة دور جديد
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-navy">
                    <tr>
                        <th style="width: 50px" class="text-center">#</th>
                        <th>اسم الدور</th>
                        <th>الصلاحيات الممنوحة</th>
                        <th class="text-center" style="width: 150px">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td class="text-center font-weight-bold">{{ sprintf('%02d', $loop->iteration) }}</td>
                            <td>
                                <span class="badge badge-info p-2">
                                    {{ $role->name == 'admin' ? 'المدير العام 🛡️' : __('permissions.' . $role->name) }}
                                </span>
                            </td>
                            <td>
                                @forelse($role->permissions as $permission)
                                    <span class="badge badge-light border ml-1 mb-1" style="font-size: 11px;">
                                        <i class="fas fa-tag text-primary ml-1"></i> {{ __('permissions.' . $permission->name) }}
                                    </span>
                                @empty
                                    <small class="text-muted">لا توجد صلاحيات</small>
                                @endforelse
                            </td>
                            <td class="text-center">
                                {{-- فصل الأزرار باستخدام btn-group أو ملاس ml-1 --}}
                                <div class="btn-group">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-info mx-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $role->id }})" class="btn btn-sm btn-outline-danger mx-1" title="حذف">
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
@stop