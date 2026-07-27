<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deshidratador Solar | Iniciar Sesión</title>

    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style (AdminLTE) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-box {
            width: 400px;
            margin: 20px;
        }

        .card {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 30px;
            text-align: center;
            color: white;
            border-bottom: none;
        }

        .card-header-custom h3 {
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .card-header-custom p {
            font-size: 14px;
            margin-bottom: 0;
            opacity: 0.9;
        }

        .card-body {
            padding: 35px 30px;
        }

        .form-control {
            border-radius: 10px;
            height: 48px;
            border: 1px solid #cbd5e1;
            padding-left: 15px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }

        .input-group-text {
            border-radius: 0 10px 10px 0 !important;
            background-color: transparent;
            border: 1px solid #cbd5e1;
            border-left: none;
            color: #64748b;
        }

        .form-control:focus + .input-group-append .input-group-text {
            border-color: #f59e0b;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            height: 48px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px 0 rgba(217, 119, 6, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px 0 rgba(217, 119, 6, 0.4);
            background: linear-gradient(135deg, #fbbf24 0%, #ea580c 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-header-custom">
            <h3><i class="fas fa-sun mr-2"></i>S.D.S.I.</h3>
            <p>Proyecto Socio Comunitario Productivo - Sistema de Deshidratación Solar Inteligente</p>
        </div>
        <div class="card-body">
            <h4 class="text-center font-weight-bold mb-4 text-dark" style="font-size: 20px;">Iniciar Sesión</h4>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" value="{{ old('usuario') }}" required autocomplete="username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-4">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block text-white">Ingresar al Sistema</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
