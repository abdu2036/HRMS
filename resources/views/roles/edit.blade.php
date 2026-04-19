@extends('layouts.admin')

@section('title', 'تعديل الدور: ' . __('permissions.' . $role->name))

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-edit ml-2"></i> تعديل الدور: {{ __('permissions.' . $role->name) }}
                </h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title float-right">تحديث بيانات الدور والصلاحيات</h3>
                    </div>
                    
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body text-right">
                            {{-- اسم الدور --}}
                            <div class="form-group mb-4">
                                <label for="name" class="font-weight-bold">
                                    اسم الدور <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" 
                                       value="{{ old('name', $role->name) }}"
                                       class="form-control @error('name') is-invalid @enderror shadow-sm" 
                                       required>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <hr>

                            {{-- الصلاحيات --}}
                            <div class="form-group mt-4">
                                <label class="font-weight-bold mb-3 d-block">
                                    <i class="fas fa-key ml-1 text-primary"></i> تعديل الصلاحيات الممنوحة:
                                </label>
                                
                                <div class="row bg-light p-3 rounded border">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox h6">
                                                <input class="custom-control-input" type="checkbox" 
                                                       name="permissions[]" 
                                                       id="perm-{{ $permission->id }}" 
                                                       value="{{ $permission->name }}"
                                                       {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-weight-normal cursor-pointer" for="perm-{{ $permission->id }}">
                                                    {{ __('permissions.' . $permission->name) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('roles.index') }}" class="btn btn-link text-muted font-weight-bold">
                                    إلغاء التعديل
                                </a>
                                <button type="submit" class="btn btn-primary px-5 shadow transform hover:scale-105 transition">
                                    <i class="fas fa-sync-alt ml-1"></i> تحديث البيانات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .card-title { float: right !important; }
        .custom-control-label::before, .custom-control-label::after {
            right: -1.5rem !important;
            left: auto !important;
        }
        .custom-control {
            padding-right: 1.5rem !important;
            padding-left: 0 !important;
        }
        .cursor-pointer { cursor: pointer; }
        .transition { transition: all 0.2s ease-in-out; }
    </style>
@stop