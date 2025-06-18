<style>
    #accordionSidebar {
        background-color: #1f1770 !important;
        /* Menambahkan !important untuk memastikan gaya diterapkan */
    }
    </style>
    
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon bg-w" style="width: 40px; height: 40px; overflow: hidden; border-radius: 50%;">
            <img src="{{ asset('log.jpg') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="sidebar-brand-text mx-3">AVENTREX</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pages.dashboard') }}">
            <i class="fas fa-fw fa-home"></i>

            <span>Dashboard</span>
        </a>
    </li>
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('template.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Documents</span></a>
    </li>
    <hr class="sidebar-divider">
    <!-- Nav Item - User -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    <!-- Nav Item - Approvals -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('approvals.index')}}">
            <i class="fas fa-fw fa-check-circle"></i>
            <span>Approvals</span></a>
    </li>
    <hr class="sidebar-divider">
    <!-- Nav Item - Laporan -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('history.index')}}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.chat') }}">
            <i class="fas fa-fw fa-comments"></i>
            <span>Chat Admin</span>
        </a>
    </li>
    <!-- Heading -->
     <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('adminlog.index') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>log aktivitas</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item">
        <a class="nav-link" href="#"
        onclick="event.preventDefault(); if (confirm('Apakah Anda yakin ingin logout?')) { document.getElementById('logout-form').submit(); }">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>


    <!-- Sidebar Toggler (Sidebar) -->

    <hr class="sidebar-divider d-none d-md-block">
    

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>


