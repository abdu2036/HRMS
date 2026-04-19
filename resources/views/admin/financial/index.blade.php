@extends('layouts.admin')
@section('title', 'سجل العمليات المالية الموحد')
@section('content')

<div class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.financial.index') }}" method="GET" class="row">
                    <div class="col-md-4">
                        <select name="employee_id" class="form-control">
                            <option value="">كل الموظفين</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">كل الأنواع</option>
                            <option value="bonus" {{ request('type') == 'bonus' ? 'selected' : '' }}>مكافآت (+)</option>
                            <option value="penalty" {{ request('type') == 'penalty' ? 'selected' : '' }}>جزاءات (-)</option>
                            <option value="custody_deficit" {{ request('type') == 'custody_deficit' ? 'selected' : '' }}>عجز عهدة (-)</option>
                            <option value="advance" {{ request('type') == 'advance' ? 'selected' : '' }}>سلف (-)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">بحث</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموظف</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>السبب/الوصف</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td>{{ $transaction->employee->full_name }}</td>
                            <td>
                                @if($transaction->type == 'bonus')
                                    <span class="badge badge-success">مكافأة</span>
                                @elseif($transaction->type == 'penalty')
                                    <span class="badge badge-danger">جزاء إداري</span>
                                @elseif($transaction->type == 'custody_deficit')
                                    <span class="badge badge-warning">عجز عهدة</span>
                                @else
                                    <span class="badge badge-info">سلفة</span>
                                @endif
                            </td>
                            <td class="{{ in_array($transaction->type, ['bonus']) ? 'text-success' : 'text-danger' }} font-weight-bold">
                                {{ in_array($transaction->type, ['bonus']) ? '+' : '-' }} {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>
                               <form id="delete-form-{{ $transaction->id }}"
                                            action="{{ route('admin.financial.destroy', $transaction->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $transaction->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">لا توجد عمليات مالية مسجلة حالياً.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection