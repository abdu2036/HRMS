@extends('layouts.admin')

@section('title', 'قائمة المكافآت')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                
                <div class="col-sm-2 text-right">
                    <a href="{{ route('admin.rewards.create') }}" class="btn btn-success shadow-sm">
                        <i class="fas fa-plus"></i> إضافة مكافأة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>الموظف</th>
                                <th>المبلغ</th>
                                <th>أيام إضافية</th>
                                <th>السبب</th>
                                <th>التاريخ</th>
                                <th width="150px">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rewards as $reward)
                                <tr>
                                    <td>
                                        <strong>{{ $reward->employee->full_name }}</strong>
                                        <br><small class="text-muted">{{ $reward->employee->employee_code }}</small>
                                    </td>
                                    <td class="text-success font-weight-bold">{{ number_format($reward->amount, 2) }} د.ل</td>
                                    <td><span class="badge badge-info">{{ $reward->days_count }} يوم</span></td>
                                    <td>{{ Str::limit($reward->description, 50) }}</td>
                                    <td>{{ $reward->date }}</td>
                                    <td>
                                        <a href="{{ route('admin.rewards.edit', $reward->id) }}"
                                            class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <form id="delete-form-{{ $reward->id }}"
                                            action="{{ route('admin.rewards.destroy', $reward->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $reward->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection