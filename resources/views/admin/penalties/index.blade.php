@extends('layouts.admin')
@section('title', 'سجل الجزاءات والخصومات')

@section('content')
  
      
                <div class="col-sm-6 text-left">
                    <a href="{{ route('admin.penalties.create') }}" class="btn btn-danger">
                        <i class="fas fa-plus"></i> إضافة جزاء جديد
                    </a>
                </div>
            <br>
    <section class="content">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموظف</th>
                            <th>المبلغ</th>
                            <th>أيام الخصم</th>
                            <th>السبب</th>
                            <th>التاريخ</th>
                            <th width="150px">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penalties as $penalty)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $penalty->employee->full_name }}</strong>
                                </td>
                                <td class="text-danger">- {{ number_format($penalty->amount, 2) }} ر.س</td>
                                <td><span class="badge badge-danger">{{ $penalty->days_count }} يوم</span></td>
                                <td>{{ Str::limit($penalty->description, 50) }}</td>
                                <td>{{ $penalty->date }}</td>
                                <td>
                                    <a href="{{ route('admin.penalties.edit', $penalty->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $penalty->id }}"
                                        action="{{ route('admin.penalties.destroy', $penalty->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $penalty->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection