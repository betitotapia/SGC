<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar acceso | SGC-SUMED</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        :root{
            --primary:#0f4c81;
            --primary-dark:#0a3558;
            --secondary:#1f9d8b;
            --bg:#f3f6fa;
            --text:#1f2937;
            --muted:#6b7280;
            --white:#ffffff;
            --danger:#dc2626;
            --border:#d7dee8;
            --shadow:0 20px 50px rgba(15, 76, 129, .20);
            --radius:24px;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(31,157,139,.20), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15,76,129,.18), transparent 30%),
                linear-gradient(135deg, #eef4fb 0%, #dfeaf7 45%, #edf5f2 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:28px;
        }

        .auth-shell{
            width:100%;
            max-width:1080px;
            min-height:620px;
            background:var(--white);
            border-radius:var(--radius);
            overflow:hidden;
            box-shadow:var(--shadow);
            display:grid;
            grid-template-columns: 1.05fr .95fr;
        }

        .brand-panel{
            background:linear-gradient(160deg, rgba(10,53,88,.95), rgba(15,76,129,.92));
            color:#fff;
            padding:54px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            position:relative;
            overflow:hidden;
        }

        .brand-panel::before{
            content:"";
            position:absolute;
            width:420px;
            height:420px;
            right:-120px;
            top:-140px;
            border-radius:50%;
            background:rgba(255,255,255,.06);
        }

        .brand-logo-wrap{
            display:flex;
            align-items:center;
            gap:18px;
            margin-bottom:28px;
            position:relative;
            z-index:2;
        }

        .brand-logo{
            width:110px;
            height:110px;
            border-radius:14px;
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            box-shadow:0 12px 28px rgba(0,0,0,.25);
        }

        .brand-logo img{
            max-width:90px;
            max-height:90px;
            object-fit:contain;
        }

        .brand-logo i{
            font-size:34px;
            color:#fff;
        }

        .brand-company{
            font-size:15px;
            letter-spacing:.12em;
            text-transform:uppercase;
            opacity:.88;
            margin-bottom:6px;
        }

        .brand-system{
            font-size:34px;
            font-weight:800;
            line-height:1.15;
            margin:0;
        }

        .brand-subtitle{
            position:relative;
            z-index:2;
            margin-top:24px;
            font-size:17px;
            line-height:1.7;
            color:rgba(255,255,255,.9);
            max-width:560px;
        }

        .brand-footer{
            position:relative;
            z-index:2;
            font-size:13px;
            color:rgba(255,255,255,.82);
            border-top:1px solid rgba(255,255,255,.14);
            padding-top:20px;
        }

        .form-panel{
            padding:56px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            background:linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .form-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            width:max-content;
            border-radius:999px;
            padding:8px 14px;
            font-size:12px;
            font-weight:700;
            background:#eef6ff;
            color:var(--primary);
            border:1px solid #d7e7f9;
            margin-bottom:18px;
        }

        .form-title{
            font-size:32px;
            font-weight:800;
            margin:0 0 8px 0;
            color:#162333;
        }

        .form-subtitle{
            margin:0 0 28px 0;
            color:var(--muted);
            font-size:15px;
            line-height:1.6;
        }

        .message-box{
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
        }

        .message-success{
            background:#ecfdf3;
            border:1px solid #abefc6;
            color:#067647;
        }

        .message-error{
            background:#fff1f2;
            border:1px solid #fecdd3;
            color:#b42318;
        }

        .field-group{ margin-bottom:20px; }

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
            font-size:15px;
        }

        .form-input{
            width:100%;
            height:52px;
            border:1px solid var(--border);
            border-radius:14px;
            background:#fff;
            outline:none;
            font-size:15px;
            color:var(--text);
            padding:0 16px 0 44px;
            transition:.22s ease;
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
            box-shadow:0 12px 28px rgba(15,76,129,.20);
        }

        .back-link{
            margin-top:16px;
            text-align:center;
        }

        .back-link a{
            color:var(--primary);
            text-decoration:none;
            font-weight:600;
            font-size:14px;
        }

        .back-link a:hover{ text-decoration:underline; }

        @media (max-width: 900px){
            .auth-shell{
                grid-template-columns:1fr;
                max-width:620px;
            }

            .brand-panel{ display:none; }
            .form-panel{ padding:38px 28px; }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="brand-panel">
            <div>
                <div class="brand-logo-wrap">
                    <div class="brand-logo">
                        @if(file_exists(public_path('img/logo-sgc.png')))
                            <img src="{{ asset('img/logo-sgc.png') }}" alt="SUMED">
                        @else
                            <i class="fas fa-shield-alt"></i>
                        @endif
                    </div>

                    <div>
                        <div class="brand-company">SUMED</div>
                        <h1 class="brand-system">SGC - Sistema de Gestión de Calidad</h1>
                    </div>
                </div>

                <div class="brand-subtitle">
                    Recuperación de acceso para usuarios autorizados del sistema institucional
                    de planes de acción, tareas, evidencias y monitoreo de eficacia.
                </div>
            </div>

            <div class="brand-footer">
                Documento de uso interno · SUMED · Sistema de Gestión de Calidad
            </div>
        </section>

        <section class="form-panel">
            <div class="form-badge">
                <i class="fas fa-envelope-open-text"></i>
                Recuperación de acceso
            </div>

            <h2 class="form-title">¿Olvidaste tu contraseña?</h2>
            <p class="form-subtitle">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            @if (session('status'))
                <div class="message-box message-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="message-box message-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="field-group">
                    <label class="field-label">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope left-icon"></i>
                        <input
                            type="email"
                            name="email"
                            class="form-input"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="usuario@sumed.com.mx">
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Enviar enlace de recuperación
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">Volver al login</a>
            </div>
        </section>
    </div>
</body>
</html>