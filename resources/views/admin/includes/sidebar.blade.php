<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="{{ url('/') }}" class="brand-link">
    <img src="{{ asset('assets/admin/dist/img/2027.png') }}"
         alt="Logo" 
         class="brand-image img-circle elevation-3" 
         style="opacity: .8; width: 33px; height: 33px; object-fit: cover;">
    
    <span class="brand-text font-weight-light">نظام إدارة الموظفين</span>
</a>

    <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        {{-- جلب الصورة عبر علاقة employee المعرفة في موديل User --}}
        <img src="{{ auth()->user()->employee && auth()->user()->employee->profile_photo ? asset('storage/' . auth()->user()->employee->profile_photo) : asset('assets/admin/dist/img/user2-160x160.jpg') }}" 
             class="img-circle elevation-2" 
             alt="User Image"
             style="width: 2.1rem; height: 2.1rem; object-fit: cover;">
    </div>
    <div class="info">
        <a href="#" class="d-block">{{ auth()->user()->name ?? 'المسؤول' }}</a>
    </div>
</div>
        

    <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        {{-- 1. ضع رابط الموظف هنا (خارج أي شرط أدمن) --}}
     @if(auth()->user()->hasRole('employee') || auth()->user()->hasRole('permissions.employee'))
    <li class="nav-item">
        <a href="{{ route('employee.dashboard') }}" 
           class="nav-link {{ request()->is('employee/dashboard*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-tie text-primary"></i>
            <p>
                لوحة تحكم الموظف 
                <span class="right badge badge-info">خاص</span>
            </p>
        </a>
    </li>
    <hr style="border-top: 1px solid #4f5962; margin: 5px 10px;">
@endif

{{-- 2. قسم الأدمن العام (super-admin) فقط --}}
@if(auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <a href="{{ url('/home') }}" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>
                @endif
            @role('super-admin')
                <li
                    class="nav-item has-treeview {{ request()->is('roles*') || request()->is('users*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('roles*') || request()->is('users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>
                            الصلاحيات والأدوار
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                     
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>إدارة الأدوار 🔑</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-info"></i>
                                <p>إدارة المستخدمين 👥</p>
                            </a>
                        </li>
                       
                    </ul>
                </li>
                @endif
            {{-- قسم الإعدادات العامة يظهر فقط للأدمن العام --}}
                @role('super-admin')
                <li class="nav-item has-treeview {{ request()->is('settings*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            الإعدادات العامة
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('company-profile.index') }}"
                                class="nav-link {{ request()->is('settings/company-profile*') ? 'active' : '' }}">
                                <i class="far fa-building nav-icon"></i>
                                <p>بيانات الشركة</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('branches.index') }}"
                                class="nav-link {{ request()->is('settings/branches*') ? 'active' : '' }}">
                                <i class="far fa-building nav-icon"></i>
                                <p>بيانات الفروع</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan


            {{-- قسم إدارة الموارد البشرية يظهر فقط للأدمن ومدير الموارد البشرية --}}
               @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager', 'Accountant' ]))
<li class="nav-item has-treeview {{ request()->is('admin/departments*') || request()->is('admin/job-titles*') || request()->is('admin/employees*') || request()->is('admin/attendances*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin/departments*') || request()->is('admin/job-titles*') || request()->is('admin/employees*') || request()->is('admin/attendances*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>
            إدارة الموارد البشرية
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager',  ]))
        <li class="nav-item">
            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->is('admin/departments*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-success"></i>
                <p>الأقسام</p>
            </a>
        </li>
        @endif
@if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager',  ]))
        <li class="nav-item">
            <a href="{{ route('job-titles.index') }}" class="nav-link {{ request()->is('admin/job-titles*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-warning"></i>
                <p>الوحدات الوظيفية</p>
            </a>
        </li>
        @endif
@if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager', ]))
        <li class="nav-item">
            <a href="{{ route('shifts.index') }}" class="nav-link {{ request()->is('admin/shifts*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-blue"></i>
                <p>الشفتات</p>
            </a>
        </li>
        @endif
@if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager',  ]))
        <li class="nav-item">
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->is('admin/employees*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-info"></i>
                <p>بيانات الموظفين</p>
            </a>
        </li>
        @endif
        @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager', 'Accountant' ]))
<li class="nav-item">
    <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ request()->is('admin/attendances*') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon text-danger"></i>
        <p>الحضور والانصراف</p>
    </a>
</li>
@endif
{{-- تابع لـ إدارة الموارد البشرية - يظهر للأدمن فقط --}}
<li class="nav-item">
    <a href="{{ route('admin.leaves.index') }}" class="nav-link {{ request()->is('admin/leaves') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon text-warning"></i>
        <p>إدارة طلبات الإجازات</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.leaves.calendar') }}" class="nav-link {{ request()->is('admin/leaves/calendar*') ? 'active' : '' }}">
        <i class="far fa-calendar-alt nav-icon text-primary"></i>
        <p>تقويم الإجازات 📅</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.rewards.index') }}" class="nav-link {{ request()->is('admin/rewards*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-gift text-warning"></i>
        <p>
            المكافآت
            <span class="badge badge-info right">جديد</span>
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.penalties.index') }}" class="nav-link {{ request()->is('admin/penalties*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-gavel text-danger"></i>
        <p>
           العقوبات والخصومات
            <span class="badge badge-danger right">جديد</span>
        </p>
    </a>
</li>
    </ul>
</li>
@endif {{-- إغلاق الشرط هنا لمدير الموارد البشرية --}}

{{-- قسم الحسابات والتقارير يظهر فقط للأدمن والمدير المالي --}}
@if(auth()->user()->hasAnyRole(['super-admin', 'Accountant']))
<li class="nav-item has-treeview {{ request()->is('admin/finance*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('admin/finance*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calculator"></i> {{-- أيقونة حاسبة أنسب للحسابات --}}
        <p>
            الحسابات والتقارير
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        
        {{-- قسم الرواتب --}}
        <li class="nav-item">
            <a href="{{ route('admin.salaries.index') }}"
               class="nav-link {{ request()->is('admin/salaries*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt nav-icon"></i>
                <p> الرواتب الأساسية </p>
            </a>
        </li>
<li class="nav-item">
    {{-- تم تغيير loans.index إلى admin.loans.index --}}
    <a href="{{ route('admin.loans.index') }}" class="nav-link {{ request()->is('admin/loans*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-usd nav-icon"></i>
        <p> السلف والقروض </p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('custodies.index') }}" class="nav-link {{ request()->is('custodies*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-boxes"></i> <p>
            إدارة عهد الموظفين
            <span class="right badge badge-info">جديد</span>
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.financial.index') }}" class="nav-link {{ request()->routeIs('admin.financial.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice-dollar text-warning"></i>
        <p>
            السجل المالي الموحد
            <span class="right badge badge-danger">جديد</span>
        </p>
    </a>
</li>
{{-- إضافة رابط صرف المرتبات هنا --}}
<li class="nav-item">
    <a href="{{ route('admin.payroll.index') }}" class="nav-link {{ request()->is('admin/payroll*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-money-check text-success"></i>
        <p>
            صرف المرتبات النهائية
            <span class="right badge badge-success">💰</span>
        </p>
    </a>
</li>

    <li class="nav-item">
    <a href="{{ route('payroll.reports.index') }}" class="nav-link {{ request()->routeIs('payroll.reports.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice-dollar text-primary"></i>
        <p>
            كشف مرتبات الموظفين
            <span class="right badge badge-primary">📊</span>
        </p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.attendances.monthly_summary') }}" class="nav-link">
        <i class="nav-icon fas fa-file-invoice-dollar text-info"></i>
        <p>ملخص الخصومات الشهري</p>
    </a>
</li>
        {{-- سنضيف السلف والعهد هنا لاحقاً بنفس الطريقة --}}
        
    </ul>
</li>
  @endif
  @php
    // جلب عدد الرسائل غير المقروءة الموجهة لهذا المستخدم أو لقسمه
    $unreadCount = \App\Models\Correspondence::where('read_at', null)
                    ->where(function($q) {
                        $q->where('receiver_id', auth()->id())
                          ->orWhere('receiver_department_id', auth()->user()->department_id);
                    })->count();
@endphp
@if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'hr-manager']))
      <li class="nav-item">
    <a href="{{ route('correspondence.index') }}" class="nav-link {{ request()->is('admin/correspondence*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-envelope-open-text"></i>
        <p>
            المراسلات الإدارية
            <span class="badge badge-info right">جديد</span>
        </p>
    </a>
</li>
@endif
                <hr style="border-top: 1px solid #4f5962;">

                <li class="nav-item">
                  <a href="{{ route('logout') }}"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    تسجيل الخروج
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
                </li>
                

            </ul>
        </nav>
    </div>
</aside>