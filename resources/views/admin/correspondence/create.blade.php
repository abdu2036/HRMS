@extends('layouts.admin')
@section('title', 'إنشاء مراسلة جديدة')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
               
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('correspondence.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="form-group">
                                <label>موضوع المراسلة</label>
                                <input type="text" name="subject" class="form-control" placeholder="اكتب عنوان الرسالة هنا..." required>
                            </div>
                            <div class="form-group">
                                <label>محتوى الرسالة</label>
                                <textarea id="compose-textarea" name="content" class="form-control" style="height: 300px"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">إعدادات المراسلة</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>نوع المراسلة</label>
                                <select name="type" id="type-select" class="form-control">
                                    <option value="internal">داخلي (رسالة عادية)</option>
                                    <option value="official">رسمي (خطاب مختوم)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>القسم المستلم</label>
                                <select name="receiver_department_id" class="form-control select2" required>
                                    <option value="">اختر القسم...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="official-options" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> سيتم توليد رقم إشاري تلقائي لهذه الرسالة.
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>مرفقات (إن وجد)</label>
                                <input type="file" name="attachment" class="form-control-file">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane"></i> إرسال المراسلة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(function () {
        // تفعيل محرر النصوص Summernote
        $('#compose-textarea').summernote({
            placeholder: 'اكتب تفاصيل الرسالة هنا...',
            tabsize: 2,
            height: 250,
            lang: 'ar-AR' // يدعم العربية
        });

        // إظهار/إخفاء خيارات الرسالة الرسمية
        $('#type-select').change(function() {
            if ($(this).val() == 'official') {
                $('#official-options').slideDown();
            } else {
                $('#official-options').slideUp();
            }
        });
    });
</script>
@endsection