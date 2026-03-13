<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña | SGC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body{
            margin:0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(90deg, #dcecf5 0%, #eef3f7 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:30px;
        }

        .auth-card{
            width:100%;
            max-width:1080px;
            min-height:620px;
            background:#fff;
            border-radius:28px;
            overflow:hidden;
            box-shadow:0 20px 50px rgba(0,0,0,.12);
            display:grid;
            grid-template-columns: 1.1fr 1fr;
        }

        .auth-left{
            background: linear-gradient(180deg, #173f67 0%, #24598d 100%);
            color:#fff;
            padding:54px;
            position:relative;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }

        .auth-left:before{
            content:'';
            position:absolute;
            width:340px;
            height:340px;
            border-radius:50%;
            background:rgba(255,255,255,.06);
            top:-60px;
            right:-60px;
        }

        .brand-top{
            position:relative;
            z-index:2;
        }

        .brand-top .mini{
            letter-spacing:2px;
            font-size:14px;
            opacity:.9;
            margin-bottom:6px;
        }

        .brand-top h1{
            margin:0;
            font-size:34px;
            line-height:1.2;
            font-weight:800;
        }

        .brand-top p{
            margin-top:28px;
            font-size:17px;
            line-height:1.7;
            max-width:520px;
        }

        .auth-footer{
            position:relative;
            z-index:2;
            font-size:14px;
            opacity:.95;
            border-top:1px solid rgba(255,255,255,.18);
            padding-top:20px;
        }

        .auth-right{
            background:#f8f8f8;
            padding:56px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:#e9f1fb;
            color:#0f4c81;
            border:1px solid #d7e4f5;
            border-radius:999px;
            padding:10px 16px;
            font-size:14px;
            font-weight:600;
            width:max-content;
            margin-bottom:22px;
        }

        .auth-right h2{
            margin:0 0 14px;
            font-size:32px;
            color:#0f2440;
            font-weight:800;
        }

        .auth-right p{
            color:#66768a;
            font-size:16px;
            line-height:1.6;
            margin-bottom:28px;
            max-width:430px;
        }

        .form-group{
            margin-bottom:18px;
        }

        label{
            display:block;
            font-weight:700;
            margin-bottom:8px;
            color:#1f2d3d;
        }

        .input-wrap{
            position:relative;
        }

        .input-wrap i{
            position:absolute;
            left:14px;
            top:50%;
            transform:translateY(-50%);
            color:#7b8ba1;
        }

        .form-control{
            width:100%;
            height:52px;
            border:1px solid #d5dce5;
            border-radius:14px;
            padding:0 16px 0 42px;
            font-size:16px;
            outline:none;
            background:#fff;
            box-sizing:border-box;
        }

        .form-control:focus{
            border-color:#2a74b8;
            box-shadow:0 0 0 3px rgba(42,116,184,.10);
        }

        .btn-submit{
            width:100%;
            height:54px;
            border:none;
            border-radius:16px;
            background: linear-gradient(90deg, #0f4c81 0%, #1fa08f 100%);
            color:#fff;
            font-size:16px;
            font-weight:800;
            cursor:pointer;
            box-shadow:0 12px 30px rgba(15,76,129,.18);
        }

        .btn-submit:hover{
            opacity:.96;
        }

        .bottom-link{
            text-align:center;
            margin-top:18px;
        }

        .bottom-link a{
            color:#0f4c81;
            text-decoration:none;
            font-weight:600;
        }

        .alert{
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
        }

        .alert-danger{
            background:#fdeaea;
            color:#9f1d1d;
            border:1px solid #f5c2c2;
        }

        .text-danger{
            color:#c62828;
            font-size:13px;
            margin-top:6px;
        }

        @media (max-width: 992px){
            .auth-card{
                grid-template-columns:1fr;
            }

            .auth-left{
                min-height:280px;
            }

            .auth-right{
                padding:32px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-left">
            <div class="brand-top">
                <div class="mini">SUMED</div>
                <h1>SGC - Sistema de Gestión de Calidad</h1>
                <p>
                    Restablecimiento de acceso para usuarios autorizados del sistema institucional
                    de planes de acción, tareas, evidencias y monitoreo de eficacia.
                </p>
            </div>

            <div class="auth-footer">
                Documento de uso interno · SUMED · Sistema de Gestión de Calidad
            </div>
        </div>

        <div class="auth-right">
            <div class="pill">
                <i class="fas fa-key"></i> Nueva contraseña
            </div>

            <h2>Restablecer contraseña</h2>
            <p>
                Ingresa tu correo electrónico y define una nueva contraseña para recuperar tu acceso.
            </p>

            @if (session('status'))
                <div class="alert alert-danger">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', request()->email) }}"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Restablecer contraseña
                </button>
            </form>

            <div class="bottom-link">
                <a href="{{ route('login') }}">Volver al login</a>
            </div>
        </div>
    </div>
</body>
</html>