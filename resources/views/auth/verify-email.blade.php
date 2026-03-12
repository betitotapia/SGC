<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar correo | SGC-SUMED</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        :root{
            --primary:#0f4c81;
            --secondary:#1f9d8b;
            --text:#1f2937;
            --muted:#6b7280;
            --white:#ffffff;
            --shadow:0 20px 50px rgba(15, 76, 129, .20);
            --radius:24px;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(31,157,139,.20), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15,76,129,.18), transparent 30%),
                linear-gradient(135deg, #eef4fb 0%, #dfeaf7 45%, #edf5f2 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:28px;
        }

        .card{
            width:100%;
            max-width:580px;
            background:#fff;
            border-radius:24px;
            box-shadow:var(--shadow);
            padding:42px;
        }

        .badge-top{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border-radius:999px;
            padding:8px 14px;
            font-size:12px;
            font-weight:700;
            background:#eef6ff;
            color:var(--primary);
            border:1px solid #d7e7f9;
            margin-bottom:18px;
        }

        .title{
            font-size:30px;
            font-weight:800;
            margin:0 0 8px;
            color:#162333;
        }

        .subtitle{
            color:var(--muted);
            font-size:15px;
            line-height:1.7;
            margin-bottom:24px;
        }

        .success-box{
            background:#ecfdf3;
            border:1px solid #abefc6;
            color:#067647;
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
        }

        .btn-primary{
            width:100%;
            height:52px;
            border:none;
            border-radius:14px;
            background:linear-gradient(90deg, var(--primary), var(--secondary));
            color:#fff;
            font-size:15px;
            font-weight:800;
            cursor:pointer;
            margin-bottom:14px;
        }

        .btn-link{
            background:none;
            border:none;
            color:var(--primary);
            font-weight:700;
            cursor:pointer;
            padding:0;
            font-size:14px;
        }

        .logout-form{
            margin-top:8px;
            text-align:center;
        }

        .actions{
            display:flex;
            flex-direction:column;
            gap:10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge-top">
            <i class="fas fa-envelope-circle-check"></i>
            Validación de cuenta
        </div>

        <h1 class="title">Verifica tu correo electrónico</h1>
        <p class="subtitle">
            Antes de continuar, revisa tu bandeja de entrada y haz clic en el enlace de verificación.
            Si no recibiste el correo, puedes solicitar uno nuevo.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="success-box">
                Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
            </div>
        @endif

        <div class="actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary">
                    Reenviar enlace de verificación
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-link">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>