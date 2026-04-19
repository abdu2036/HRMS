  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-comments"></i>
        @if($unreadMails->count() > 0)
            <span class="badge badge-danger navbar-badge">{{ $unreadMails->count() }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header text-right">{{ $unreadMails->count() }} مراسلات جديدة</span>
        
        @foreach($unreadMails as $mail)
        <a href="{{ route('correspondence.show', $mail->id) }}" class="dropdown-item">
            <div class="media">
                {{-- نستخدم أيقونة افتراضية أو صورة الموظف إذا وجدت --}}
                <img src="{{ asset('assets/admin/dist/img/user-avatar.png') }}" alt="User Avatar" class="img-size-50 ml-3 img-circle">
                <div class="media-body text-right">
                    <h3 class="dropdown-item-title font-weight-bold">
                        {{ $mail->sender->name }}
                        <span class="float-left text-sm text-danger"><i class="fas fa-star"></i></span>
                    </h3>
                    <p class="text-sm text-dark">{{ Str::limit($mail->subject, 25) }}</p>
                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> {{ $mail->created_at->diffForHumans() }}</p>
                </div>
            </div>
            </a>
        <div class="dropdown-divider"></div>
        @endforeach

        <a href="{{ route('correspondence.index') }}" class="dropdown-item dropdown-footer text-center">مشاهدة كافة المراسلات</a>
    </div>
</li>
      <!-- Notifications Dropdown Menu -->
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="badge badge-warning navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header">{{ auth()->user()->unreadNotifications->count() }} إشعارات جديدة</span>
        
        @foreach(auth()->user()->unreadNotifications as $notification)
            <div class="dropdown-divider"></div>
            
            {{-- التعديل هنا: نمرر معرف الإشعار لدالة القراءة --}}
            <a href="{{ route('admin.notifications.read', $notification->id) }}" class="dropdown-item">
                
                <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} mr-2 {{ $notification->data['color'] ?? 'text-primary' }}"></i> 
                
                <span class="text-sm">
                    {{ Str::limit($notification->data['title'], 28) }}
                </span>
                
                <span class="float-right text-muted text-sm">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </a>
        @endforeach

        <div class="dropdown-divider"></div>
<a href="{{ route('admin.notifications.all') }}" class="dropdown-item dropdown-footer">عرض كل الإشعارات</a>
    </div>
</li>
      
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
            class="fas fa-th-large"></i></a>
      </li>
      
    </ul>
  </nav>