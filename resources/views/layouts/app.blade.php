<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Deshidratación Solar | @yield('title', 'Dashboard')</title>

    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style (AdminLTE) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Custom Design Tokens for Rich Aesthetics -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f4f6f9;
        }
        
        .main-sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.15);
        }
        
        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        
        .nav-sidebar .nav-link.active {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px 0 rgba(59, 130, 246, 0.3) !important;
            border-radius: 8px;
        }

        .nav-sidebar .nav-link {
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s ease-in-out;
        }

        .nav-sidebar .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            transform: translateX(3px);
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px 0 rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        /* Glassmorphism styling for metric cards */
        .metric-card {
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .metric-card::after {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        .bg-temp {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-hum {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .bg-pres {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-update {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @yield('styles')
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
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Profile Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user mr-1"></i>
                    <span class="d-none d-md-inline font-weight-bold">{{ session('nombre_completo') }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header font-weight-bold">Información de Cuenta</span>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item">
                        <i class="fas fa-id-card mr-2"></i> Rol: <span class="badge badge-info">{{ session('rol') }}</span>
                    </div>
                    <div class="dropdown-item">
                        <i class="fas fa-user-circle mr-2"></i> Usuario: <code>{{ session('usuario_username') }}</code>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer text-danger font-weight-bold">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link text-center py-3">
            <span class="brand-text font-weight-bold text-white text-lg tracking-wider">
                <i class="fas fa-sun mr-2 text-warning"></i>DESHIDRATADOR
            </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar px-3">
            <!-- Sidebar Menu -->
            <nav class="mt-4">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') || Route::is('home') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('historial.index') }}" class="nav-link {{ Route::is('historial.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Historial de Lecturas</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('frutas.index') }}" class="nav-link {{ Route::is('frutas.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-apple-alt"></i>
                            <p>Frutas</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('procesos.index') }}" class="nav-link {{ Route::is('procesos.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-spinner"></i>
                            <p>Procesos de Deshidratación</p>
                        </a>
                    </li>

                    <li class="nav-item mt-4">
                        <a href="{{ route('logout') }}" class="nav-link text-danger">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p class="font-weight-bold">Cerrar Sesión</p>
                        </a>
                    </li>

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
                        <h1 class="m-0 font-weight-bold text-dark">@yield('page_title')</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                
                <!-- Display Success/Error notifications -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                        <h5><i class="icon fas fa-check-circle"></i> ¡Éxito!</h5>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                        <h5><i class="icon fas fa-ban"></i> Error</h5>
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer bg-light border-top">
        <strong>Instituto Técnico Nacional de Comercio "Federico Alvarez Plata" </strong>
        <div class="">
            Proyecto Socio Comunitario Productivo - Derechos reservados &copy; 2026 <br>
        </div> 
         
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@yield('scripts')
</body>
</html>
