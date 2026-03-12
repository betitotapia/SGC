<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SGC-SUMED</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <style>
        :root{
            --primary:#0f4c81;
            --primary-dark:#0a3558;
            --secondary:#1f9d8b;
            --accent:#f4b400;
            --bg:#f3f6fa;
            --text:#1f2937;
            --muted:#6b7280;
            --white:#ffffff;
            --danger:#dc2626;
            --border:#d7dee8;
            --shadow:0 20px 50px rgba(15, 76, 129, .20);
            --radius:24px;
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            min-height:100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
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

        .login-shell{
            width:100%;
            max-width:1180px;
            min-height:680px;
            background:var(--white);
            border-radius:var(--radius);
            overflow:hidden;
            box-shadow:var(--shadow);
            display:grid;
            grid-template-columns: 1.08fr .92fr;
            border:1px solid rgba(255,255,255,.8);
        }

        .brand-panel{
            position:relative;
            background:
                linear-gradient(160deg, rgba(10,53,88,.95), rgba(15,76,129,.92)),
                linear-gradient(120deg, var(--primary-dark), var(--primary));
            color:var(--white);
            padding:54px 54px 42px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            overflow:hidden;
        }

        .brand-panel::before{
            content:"";
            position:absolute;
            width:420px;
            height:420px;
            right:-110px;
            top:-120px;
            border-radius:50%;
            background:rgba(255,255,255,.07);
        }

        .brand-panel::after{
            content:"";
            position:absolute;
            width:300px;
            height:300px;
            left:-90px;
            bottom:-110px;
            border-radius:50%;
            background:rgba(31,157,139,.16);
        }

        .brand-top{
            position:relative;
            z-index:2;
        }

        .brand-logo-wrap{
            display:flex;
            align-items:center;
            gap:18px;
            margin-bottom:28px;
        }

        
    .brand-logo{
            width:150px;
            height:auto;
            border-radius:14px; /* más cuadrado */
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            backdrop-filter: blur(6px);
            box-shadow:0 12px 28px rgba(0,0,0,.25);
            transition:.3s ease;
        }
        .brand-logo:hover{
            transform:scale(1.04);
            box-shadow:0 18px 40px rgba(0,0,0,.35);
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
            font-size:36px;
            font-weight:800;
            line-height:1.1;
            margin:0;
        }

        .brand-subtitle{
            margin-top:20px;
            font-size:17px;
            line-height:1.7;
            color:rgba(255,255,255,.9);
            max-width:560px;
        }

        .brand-cards{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
            margin-top:34px;
            position:relative;
            z-index:2;
        }

        .brand-card{
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.12);
            border-radius:18px;
            padding:18px;
            backdrop-filter: blur(4px);
        }

        .brand-card h4{
            margin:0 0 10px 0;
            font-size:15px;
            font-weight:700;
        }

        .brand-card p{
            margin:0;
            font-size:14px;
            line-height:1.5;
            color:rgba(255,255,255,.88);
        }

        .brand-footer{
            position:relative;
            z-index:2;
            margin-top:28px;
            padding-top:20px;
            border-top:1px solid rgba(255,255,255,.14);
            font-size:13px;
            color:rgba(255,255,255,.8);
        }

        .form-panel{
            padding:56px 56px 42px;
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
            font-size:34px;
            font-weight:800;
            margin:0 0 8px 0;
            color:#162333;
        }

        .form-subtitle{
            margin:0 0 30px 0;
            color:var(--muted);
            font-size:15px;
            line-height:1.6;
        }

        .error-box{
            background:#fff1f2;
            border:1px solid #fecdd3;
            color:#b42318;
            border-radius:16px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:14px;
        }

        .field-group{
            margin-bottom:20px;
        }

        .field-label{
            display:block;
            margin-bottom:9px;
            font-size:14px;
            font-weight:700;
            color:#334155;
        }

        .input-wrap{
            position:relative;
        }

        .left-icon{
            position:absolute;
            left:15px;
            top:50%;
            transform:translateY(-50%);
            color:#7b8794;
            font-size:15px;
        }

        .right-action{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            color:#7b8794;
            font-size:15px;
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
            color:var(--text);
            padding:0 45px 0 44px;
            transition:.22s ease;
        }

        .form-input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 4px rgba(15,76,129,.10);
        }

        .row-meta{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            margin-bottom:24px;
            font-size:14px;
        }

        .remember-label{
            display:flex;
            align-items:center;
            gap:8px;
            color:#475569;
            cursor:pointer;
        }

        .remember-label input{
            accent-color:var(--primary);
        }

        .forgot-link{
            color:var(--primary);
            text-decoration:none;
            font-weight:600;
        }

        .forgot-link:hover{
            text-decoration:underline;
        }

        .login-btn{
            width:100%;
            height:54px;
            border:none;
            border-radius:14px;
            background:linear-gradient(90deg, var(--primary), var(--secondary));
            color:#fff;
            font-size:15px;
            font-weight:800;
            letter-spacing:.02em;
            cursor:pointer;
            transition:.25s ease;
            box-shadow:0 12px 28px rgba(15,76,129,.20);
        }

        .login-btn:hover{
            transform:translateY(-1px);
            box-shadow:0 16px 32px rgba(15,76,129,.26);
        }

        .security-note{
            margin-top:20px;
            padding:14px 16px;
            border-radius:14px;
            background:#f8fbff;
            border:1px solid #e4edf6;
            color:#5b6777;
            font-size:13px;
            line-height:1.6;
        }

        .security-note i{
            color:var(--secondary);
            margin-right:6px;
        }

        .footer-note{
            margin-top:20px;
            text-align:center;
            color:#7b8794;
            font-size:13px;
        }

        @media (max-width: 960px){
            .login-shell{
                grid-template-columns:1fr;
                max-width:620px;
            }

            .brand-panel{
                padding:34px 30px;
            }

            .brand-cards{
                grid-template-columns:1fr;
            }

            .form-panel{
                padding:38px 30px 30px;
            }
        }

        @media (max-width: 580px){
            body{
                padding:12px;
            }

            .login-shell{
                min-height:auto;
                border-radius:18px;
            }

            .brand-system{
                font-size:28px;
            }

            .form-title{
                font-size:28px;
            }

            .row-meta{
                flex-direction:column;
                align-items:flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">

        <section class="brand-panel">
            <div class="brand-top">
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
                    Plataforma integral para el control de planes de acción, tareas, evidencias,
                    monitoreo de eficacia, trazabilidad y seguimiento de mejora continua.
                </div>

                <div class="brand-cards">
                    <div class="brand-card">
                        <h4><i class="fas fa-clipboard-check"></i> Planes de acción</h4>
                        <p>Control centralizado de hallazgos, responsables, fechas compromiso y seguimiento.</p>
                    </div>

                    <div class="brand-card">
                        <h4><i class="fas fa-tasks"></i> Tareas y evidencias</h4>
                        <p>Asignación de actividades, revisión, comentarios y carga documental por tarea.</p>
                    </div>

                    <div class="brand-card">
                        <h4><i class="fas fa-chart-line"></i> Monitoreo de eficacia</h4>
                        <p>Evaluación posterior al cierre para validar cumplimiento y efectividad real.</p>
                    </div>

                    <div class="brand-card">
                        <h4><i class="fas fa-bell"></i> Notificaciones</h4>
                        <p>Alertas internas, correos y seguimiento oportuno para responsables y calidad.</p>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                Documento de uso interno · SUMED · Sistema de Gestión de Calidad
            </div>
        </section>

        <section class="form-panel">
            <div class="form-badge">
                <i class="fas fa-lock"></i>
                Acceso seguro al sistema
            </div>

            <h2 class="form-title">Bienvenido</h2>
            <p class="form-subtitle">
                Ingresa tus credenciales para acceder al sistema institucional de gestión de calidad.
            </p>

            @if ($errors->any())
                <div class="error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="error-box" style="background:#ecfdf3;border-color:#abefc6;color:#067647;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
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
                            autocomplete="username"
                            placeholder="usuario@sumed.com.mx">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-key left-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-input"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••">
                        <i class="fas fa-eye right-action" id="togglePassword"></i>
                    </div>
                </div>

                <div class="row-meta">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        Recordar sesión
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    Iniciar sesión
                </button>
            </form>

            <div class="security-note">
                <i class="fas fa-shield-alt"></i>
                El acceso a esta plataforma está destinado únicamente a personal autorizado.
                Toda actividad puede quedar registrada para trazabilidad y control interno.
            </div>

            <div class="footer-note">
                SGC-SUMED · Mejora continua · Control documental · Seguimiento de acciones
            </div>
        </section>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');

        togglePassword?.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.querySelector('form')?.addEventListener('submit', function () {
            loginBtn.disabled = true;
            loginBtn.innerText = 'Ingresando...';
        });
    </script>
</body>
</html>