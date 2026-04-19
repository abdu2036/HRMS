@extends('layouts.admin')

{{-- تحديد العناوين الديناميكية --}}
@section('title', 'إدارة الشركات')
@section('content_header', 'إدارة الشركات الأساسية')

@section('content')
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">بيانات الشركة 🏢</h3>
            <div class="card-tools">
                @if(!$company)
                    <a href="{{ route('company-profile.create') }}" class="btn btn-primary btn-sm">إنشاء بروفايل جديد +</a>
                @endif
                @if($company)
                    {{-- الأزرار في رأس البطاقة أو الجدول --}}
                    <div class="mt-3">
                        <a href="{{ route('company-profile.edit', $company->id) }}" class="btn btn-warning">تعديل ✏️</a>

                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $company->id }})">
                            حذف البروفايل 🗑️
                        </button>

                        <form id="delete-form-{{ $company->id }}" action="{{ route('company-profile.destroy', $company->id) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if($company)
                <div class="row">
                    <div class="col-md-4 text-center border-left">
                        @if($company->company_logo)
                            <img src="{{ asset('storage/' . $company->company_logo) }}" class="img-fluid rounded shadow-sm"
                                style="max-height: 180px;">
                        @else
                            <div class="p-5 bg-light border rounded">لا يوجد شعار 🖼️</div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <table class="table table-hover">
                            <tr>
                                <th width="30%">اسم الشركة</th>
                                <td>{{ $company->company_name }}</td>
                            </tr>
                            <tr>
                                <th>العنوان</th>
                                <td>{{ $company->address }}</td>
                            </tr>
                            <tr>
                                <th>الهاتف</th>
                                <td>{{ $company->company_phone ?? '---' }}</td>
                            </tr>
                            <tr>
                                <th>الإيميل</th>
                                <td>{{ $company->company_email ?? '---' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">لم يتم إعداد بيانات الشركة بعد.</div>
            @endif
        </div>
        
    </div>
@endsection