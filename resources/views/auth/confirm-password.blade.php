<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar contraseña | SGC-SUMED</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        :root{
            --primary:#0f4c81;
            --secondary:#1f9d8b;
            --text:#1f2937;
            --muted:#6b7280;
            --white:#ffffff;
            --border:#d7dee8;
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
            max-width:520px;
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
            line-height:1.6;
            margin-bottom:28px;
        }

        .error-box{
            background:#fff1f2;
            border:1px solid #fecdd3;
            color:#b42318;
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
        }

        .field-label{
            display:block;
            margin-bottom:9px;
            font-size:14px;
            font-weight:700;
            color:#334155;
        }

        .input-wrap{ position:relative; }
        .left-icon{
            position:absolute;
            left:15px;
            top:50%;
            transform:translateY(-50%);
            color:#7b8794;
        }

        .right-action{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            color:#7b8794;
            cursor:pointer;
        }

        .form-input{
            width:100%;
            height:52px;
            border:1px solid var(--border);
            border-radius:14px;
            background:#fff;
            outline:none;
            font-size:15px;
            color:#1f2937;
            padding:0 42px 0 44px;
            transition:.22s ease;
            margin-bottom:22px;
        }

        .form-input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 4px rgba(15,76,129,.10);
        }

        .submit-btn{
            width:100%;
            height:54px;
            border:none;
            border-radius:14px;
            background:linear-gradient(90deg, var(--primary), var(--secondary));
            color:#fff;
            font-size:15px;
            font-weight:800;
            cursor:pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge-top">
            <i class="fas fa-shield-alt"></i>
            Confirmación de seguridad
        </div>

        <h1 class="title">Confirma tu contraseña</h1>
        <p class="subtitle">
            Esta acción requiere validación adicional. Ingresa tu contraseña para continuar.
        </p>

        @if ($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <label class="field-label">Contraseña</label>
            <div class="input-wrap">
                <i class="fas fa-lock left-icon"></i>
                <input type="password" name="password" id="password" class="form-input" required autocomplete="current-password">
                <i class="fas fa-eye right-action" id="togglePassword"></i>
            </div>

            <button type="submit" class="submit-btn">Confirmar</button>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword?.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>