@extends('layouts.admin')
@section('title', 'تفاصيل المراسلة')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
           
            <div class="col-sm-12 text-right">
                <a href="{{ route('correspondence.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-right"></i> عودة للصندوق
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline {{ $correspondence->type == 'official' ? 'card-danger' : 'card-primary' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if($correspondence->type == 'official')
                                <i class="fas fa-stamp text-danger"></i> مراسلة رسمية رقم: <strong>{{ $correspondence->reference_number }}</strong>
                            @else
                                <i class="fas fa-envelope text-primary"></i> مراسلة داخلية
                            @endif
                        </h3>
                        <div class="card-tools">
                            @if($correspondence->type == 'official')
                                <a href="{{ route('correspondence.print', $correspondence->id) }}" target="_blank" class="btn btn-tool">
                                    <i class="fas fa-print"></i> طباعة كخطاب رسمي
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="mailbox-read-info">
                            <h5>الموضوع: {{ $correspondence->subject }}</h5>
                            <h6>من: {{ $correspondence->sender->name }}
                                <span class="mailbox-read-time float-left">{{ $correspondence->created_at->format('d M, Y H:i A') }}</span>
                            </h6>
                            <h6>إلى: {{ $correspondence->department->name ?? 'موظف محدد' }}</h6>
                        </div>
                        
                        <div class="mailbox-read-message p-4" style="background: #f9f9f9; min-height: 200px;">
                            {!! $correspondence->content !!}
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        @if($correspondence->read_at)
                            <small class="text-success">
                                <i class="fas fa-check-double"></i> تمت قراءة هذه الرسالة في: {{ $correspondence->read_at->format('Y-m-d H:i') }}
                            </small>
                        @endif
                        
                        @if($correspondence->attachment)
                            <ul class="mailbox-attachments d-flex align-items-stretch clearfix mt-3">
                                <li>
                                    <span class="mailbox-attachment-icon"><i class="far fa-file-pdf"></i></span>
                                    <div class="mailbox-attachment-info">
                                        <a href="{{ asset('storage/' . $correspondence->attachment) }}" class="mailbox-attachment-name" target="_blank">المرفق.pdf</a>
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection