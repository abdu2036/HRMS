@extends('layouts.admin')

@section('title', 'إضافة شفت جديد')

@section('content')
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary card-outline shadow">
                    <div class="card-header bg-white">
                        <h3 class="card-title text-bold">بيانات الشفت الجديد ⏰</h3>
                    </div>
                    <form action="{{ route('shifts.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>اسم الشفت <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="مثلاً: الشفت الصباحي"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>وقت الحضور (بداية الشفت)</label>
                                        <input type="time" name="start_time" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>وقت الانصراف (نهاية الشفت)</label>
                                        <input type="time" name="end_time" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>مدة الاستراحة (بالدقائق)</label>
                                        <input type="number" name="break_duration" class="form-control" value="60">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>الحالة</label>
                                        <select name="status" class="form-control">
                                            <option value="1">نشط</option>
                                            <option value="0">متوقف</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>وصف الشفت</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="ملاحظات إضافية حول هذا الشفت..."></textarea>
                            </div>
                        </div>

                        <div class="card-footer bg-white text-right">
                            <button type="submit" class="btn btn-primary px-4">حفظ الشفت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection