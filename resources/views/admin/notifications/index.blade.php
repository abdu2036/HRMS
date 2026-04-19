@extends('layouts.admin')
@section('title', 'مركز الإشعارات')
@section('content')


<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">كافة التنبيهات الواردة</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <li class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}" style="border-right: 5px solid {{ $notification->read_at ? '#ddd' : '#007bff' }}">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fa-2x {{ $notification->data['color'] ?? 'text-secondary' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        {{ $notification->data['title'] }}
                                        @if(!$notification->read_at)
                                            <span class="badge badge-danger ml-2">جديد</span>
                                        @endif
                                    </h5>
                                    <p class="mb-1 text-muted">{{ $notification->data['body'] ?? '' }}</p>
                                    <small class="text-secondary">
                                        <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-5 text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <p>لا توجد إشعارات مسجلة حالياً.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer px-3">
                {{ $notifications->links() }} {{-- روابط التنقل بين الصفحات --}}
            </div>
        </div>
    </div>
</section>
@endsection