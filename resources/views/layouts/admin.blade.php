<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | {{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('site_favicon', asset('logo/favicon.png')) }}">
    
    @vite(['resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm nav-link text-white">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link text-center border-bottom-0 py-3">
      @if(\App\Models\Setting::get('site_logo'))
        <img src="{{ \App\Models\Setting::get('site_logo') }}" alt="Logo" class="img-circle elevation-3 bg-white p-1" style="width: 70px; height: 70px; float: none; max-height: none;">
      @else
        <span class="brand-text font-weight-bold" style="font-size: 1.2rem;">{{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }}</span>
      @endif
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block text-warning font-weight-bold text-uppercase">
            <i class="fas fa-user-circle mr-2"></i> {{ auth()->user()->name ?? 'Admin' }}
          </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          {{-- Management Header --}}
          @if(auth()->user()->can('view-classes') || auth()->user()->can('view-sections') || auth()->user()->can('view-users') || auth()->user()->can('manage-roles-permissions'))
          <li class="nav-header">MANAGEMENT</li>
          
          @can('view-classes')
          <li class="nav-item">
            <a href="{{ route('classes.index') }}" class="nav-link {{ request()->is('classes*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-school"></i>
              <p>Classes</p>
            </a>
          </li>
          @endcan

          @can('view-sections')
          <li class="nav-item">
            <a href="{{ route('sections.index') }}" class="nav-link {{ request()->is('sections*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-door-open"></i>
              <p>Sections</p>
            </a>
          </li>
          @endcan

          @can('view-users')
          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>
          @endcan

          @can('view-timetables')
          <li class="nav-item">
            <a href="{{ route('timetables.index') }}" class="nav-link {{ request()->is('timetables*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-clock"></i>
              <p>Timetables</p>
            </a>
          </li>
          @endcan

          @can('view-devices')
          <li class="nav-item">
            <a href="{{ route('devices.index') }}" class="nav-link {{ request()->is('devices*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-desktop"></i>
              <p>Devices</p>
            </a>
          </li>
          @endcan

          @can('view-holidays')
          <li class="nav-item">
            <a href="{{ route('holidays.index') }}" class="nav-link {{ request()->is('holidays*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Holidays</p>
            </a>
          </li>
          @endcan

          @can('view-leaves')
          <li class="nav-item">
            <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->is('leaves*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Leaves</p>
            </a>
          </li>
          @endcan

          @can('manage-roles-permissions')
          <li class="nav-item {{ request()->is('roles*') || request()->is('permissions*') || request()->is('settings*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('roles*') || request()->is('permissions*') || request()->is('settings*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                RBAC Settings 
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->is('permissions*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Permissions</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>System Settings</p>
                </a>
              </li>
            </ul>
          </li>
          @endcan
          @endif

          {{-- Attendance --}}
          @can('view-attendance')
          <li class="nav-header">ATTENDANCE</li>
          <li class="nav-item">
            <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->is('attendances*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>Logs</p>
            </a>
          </li>
          @endcan
          
          {{-- Reports --}}
          @can('view-reports')
          <li class="nav-header">REPORTS</li>
          <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Reports</p>
            </a>
          </li>
          @endcan
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Success!</h5>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
        Developer: Saiful Islam | v1.0.0
    </div>
    <strong>{!! \App\Models\Setting::get('footer_text', 'Copyright &copy; ' . date('Y') . ' <a href="#">' . config('app.name') . '</a>. All rights reserved.') !!}</strong>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@stack('scripts')
<script>
$(function() {
    // Global Confirmation for Delete Buttons
    $(document).on('click', '.delete-confirm', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const message = $(this).data('message') || 'You won\'t be able to revert this!';
        
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
</body>
</html>
