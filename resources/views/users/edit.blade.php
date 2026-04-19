@extends('layouts.admin')

@section('title', 'تعديل صلاحيات المستخدم')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-shield ml-2 text-primary"></i> تعديل صلاحيات المستخدم
                </h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-outline card-primary shadow-lg border-0">
                    <div class="card-header bg-navy py-3">
                        <h3 class="card-title float-right font-weight-bold text-white">
                             بيانات الحساب: {{ $user->name }}
                        </h3>
                    </div>

                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body text-right">
                            {{-- اسم المستخدم (للقراءة فقط) --}}
                            <div class="form-group mb-4">
                                <label class="text-navy font-weight-bold">
                                    <i class="fas fa-user ml-1"></i> اسم المستخدم
                                </label>
                                <div class="form-control bg-light border-0 shadow-sm font-weight-bold py-2 h-auto">
                                    {{ $user->name }}
                                </div>
                            </div>

                            {{-- البريد الإلكتروني (للقراءة فقط) --}}
                            <div class="form-group mb-4">
                                <label class="text-navy font-weight-bold">
                                    <i class="fas fa-envelope ml-1"></i> البريد الإلكتروني
                                </label>
                                <div class="form-control bg-light border-0 shadow-sm text-muted py-2 h-auto">
                                    {{ $user->email }}
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- الإضافة الجديدة: ربط الموظف --}}
                            <div class="form-group mb-4">
                                <label for="employee_id" class="text-navy font-weight-bold">
                                    <i class="fas fa-id-card ml-1"></i> ربط الحساب بملف الموظف
                                </label>
                                <select name="employee_id" id="employee_id" class="form-control custom-select shadow-sm @error('employee_id') is-invalid @enderror" style="height: 50px; font-weight: bold;">
                                    <option value="">-- اختر الموظف لربطه بهذا الحساب --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $user->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->department->name ?? 'بدون قسم' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="text-danger mt-2 d-block">
                                    <i class="fas fa-exclamation-triangle ml-1"></i> يجب اختيار الموظف الصحيح لضمان وصول مراسلات القسم إليه.
                                </small>
                            </div>

                            {{-- اختيار الدور الجديد --}}
                            <div class="form-group">
                                <label for="role" class="text-navy font-weight-bold">
                                    <i class="fas fa-key ml-1"></i> تعيين دور جديد (الصلاحية)
                                </label>
                                <select name="role" id="role" class="form-control custom-select shadow-sm @error('role') is-invalid @enderror" style="height: 50px; font-weight: bold;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- أزرار التحكم --}}
                        <div class="card-footer bg-white border-top py-4">
                            <button type="submit" class="btn btn-primary btn-block shadow font-weight-bold py-3 transition-transform hover:scale-102">
                                <i class="fas fa-save ml-2"></i> حفظ التعديلات والربط الوظيفي
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-link btn-block text-muted font-weight-bold mt-2">
                                إلغاء والعودة
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
        body { font-family: 'Cairo', sans-serif !important; }
        .bg-navy { background-color: #001f3f !important; }
        .text-navy { color: #001f3f !important; }
        .card-title { float: right !important; }
        .custom-select { padding-right: 1.0rem !important; }
        .hover\:scale-102:hover { transform: scale(1.02); }
        .transition-transform { transition: transform 0.2s ease-in-out; }
    </style>
@stop