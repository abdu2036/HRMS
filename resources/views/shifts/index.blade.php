@extends('layouts.admin')

@section('title', 'قائمة الشفتات')

@section('content')
<div class="container-fluid pt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center">
            <h3 class="card-title flex-grow-1 font-weight-bold">نظام الورديات (الشفتات) ⏰</h3>
            <a href="{{ route('shifts.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> إضافة شفت جديد
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>اسم الشفت</th>
                        <th>الحضور</th>
                        <th>الانصراف</th>
                        <th>الاستراحة</th>
                        <th>الحالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $shift->name }}</strong>
                                @if($shift->description)
                                    <br><small class="text-muted">{{ $shift->description }}</small>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ date('h:i A', strtotime($shift->start_time)) }}</span></td>
                            <td><span class="badge badge-warning">{{ date('h:i A', strtotime($shift->end_time)) }}</span></td>
                            <td>{{ $shift->break_duration }} دقيقة</td>
                            <td>
                                {!! $shift->status ? '<span class="badge badge-success">نشط</span>' : '<span class="badge badge-danger">متوقف</span>' !!}
                            </td>
                            <td class="text-center">
    <div class="d-flex justify-content-center" style="gap: 10px;">
        <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-sm btn-info shadow-sm">
            <i class="fas fa-edit"></i>
        </a>

        <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" id="delete-form-{{ $shift->id }}">
    @csrf 
    @method('DELETE')
    <button type="button" class="btn btn-sm btn-danger shadow-sm" onclick="confirmDelete({{ $shift->id }})">
        <i class="fas fa-trash"></i>
    </button>
</form>
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">لا توجد شفتات مضافة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection