@extends('layouts.admin')

@section('title', 'بريد المراسلات الإدارية')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        
            <div class="col-sm-6 text-left">
                <a href="{{ route('correspondence.create') }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus-circle"></i> إنشاء مراسلة جديدة
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title float-right">قائمة المراسلات (الوارد والصادر)</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>النوع</th>
                            <th>الرقم الإشاري</th>
                            <th>الموضوع</th>
                            <th>المرسل</th>
                            <th>المستلم</th>
                            <th>المرفق</th> {{-- عمود جديد للمرفقات --}}
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($correspondences as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($item->type == 'official')
                                    <span class="badge badge-danger p-2"><i class="fas fa-stamp"></i> رسمي</span>
                                @else
                                    <span class="badge badge-secondary p-2">داخلي</span>
                                @endif
                            </td>
                            <td><strong class="text-primary">{{ $item->reference_number ?? '---' }}</strong></td>
                            <td class="text-right">{{ Str::limit($item->subject, 40) }}</td>
                            <td><span class="badge badge-light border">{{ $item->sender->name }}</span></td>
                            <td>
                                {{-- عرض القسم أو اسم الموظف المستلم --}}
                                @if($item->department)
                                    <span class="text-navy"><i class="fas fa-building small"></i> {{ $item->department->name }}</span>
                                @elseif($item->receiver_id)
                                    <span class="text-muted"><i class="fas fa-user small"></i> مستلم محدد</span>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                {{-- أيقونة المرفق إذا وجد --}}
                                @if($item->attachment)
                                    <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank" class="text-info" title="عرض المرفق">
                                        <i class="fas fa-paperclip fa-lg"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300"><i class="fas fa-times small"></i></span>
                                @endif
                            </td>
                            <td class="small">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($item->read_at)
                                    <span class="badge badge-success"><i class="fas fa-check-double small"></i> مقروءة</span>
                                @else
                                    <span class="badge badge-warning text-white"><i class="fas fa-clock small"></i> جديدة</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('correspondence.show', $item->id) }}" class="btn btn-sm btn-info shadow-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->type == 'official')
                                    <a href="{{ route('correspondence.print', $item->id) }}" class="btn btn-sm btn-dark shadow-sm" target="_blank" title="طباعة">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center p-5">
                                <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">لا توجد مراسلات حالياً في بريدك</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- التعديل المهم هنا: التأكد من عمل الروابط بشكل صحيح --}}
            <div class="card-footer clearfix">
                <div class="float-left">
                    {{ $correspondences->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('css')
<style>
    .table td, .table th { vertical-align: middle !important; }
    .badge { font-weight: 500; }
    .text-navy { color: #001f3f; font-weight: bold; }
</style>
@endsection