<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $__env->yieldContent('title', 'GANA FÁCIL'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --bg:#0f0f10;
      --card:#1a1a1d;
      --border:rgba(255,255,255,.10);
      --text:rgba(255,255,255,.92);
      --muted:rgba(255,255,255,.70);
      --muted2:rgba(255,255,255,.55);
      --red:#ff2a2a;
      --red2:#d40000;
    }

    body{
      margin:0;
      font-family: system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    /* Fondo con brillo suave */
    .auth-wrap{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:32px 16px;
      background:
        radial-gradient(1200px 600px at 20% 10%, rgba(255,0,0,.10), transparent 60%),
        radial-gradient(1000px 500px at 80% 20%, rgba(255,0,0,.08), transparent 60%),
        var(--bg);
    }

    .auth-card{
      /* Más estrecho para que el login no se vea "ancho" */
      width:min(420px, 100%);
      background: rgba(26,26,29,.95);
      border:1px solid var(--border);
      border-radius:18px;
      box-shadow: 0 18px 50px rgba(0,0,0,.55);
      overflow:hidden;
    }

    .auth-head{
      padding:22px 22px 10px;
      text-align:center;
    }

    .auth-brand{
      margin:0;
      font-weight: 900;
      letter-spacing: .08em;
      color: var(--red);
      font-size: 30px;
    }

    .auth-sub{
      margin:8px 0 0;
      color: var(--muted);
      font-size: 14px;
    }

    .auth-body{ padding:18px 22px 22px; }

    .auth-grid{
      display:grid;
      grid-template-columns: 1fr;
      gap:14px;
    }

    .auth-label{
      display:flex;
      justify-content:space-between;
      align-items:baseline;
      gap:12px;
      color: var(--text);
      font-weight: 700;
      font-size: 14px;
      margin-bottom: 6px;
    }

    .auth-hint{
      color: var(--muted2);
      font-weight: 600;
      font-size: 12px;
    }

    .auth-input{
      width:100%;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.12);
      color:#fff;
      border-radius:12px;
      padding:12px 14px;
      outline:none;
    }
    .auth-input::placeholder{ color: rgba(255,255,255,.35); }

    .auth-input:focus{
      border-color: rgba(255,42,42,.65);
      box-shadow: 0 0 0 4px rgba(255,42,42,.12);
    }

    .auth-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      margin-top: 4px;
    }

    .auth-check{
      display:flex;
      align-items:center;
      gap:8px;
      color: var(--muted);
      font-size: 13px;
    }

    .auth-link{
      color: #ff5151;
      text-decoration:none;
      font-weight: 800;
    }
    .auth-link:hover{ text-decoration: underline; }

    .auth-btn{
      width:100%;
      border:none;
      border-radius:12px;
      padding:12px 14px;
      background: linear-gradient(180deg, var(--red), var(--red2));
      color:#fff;
      font-weight: 900;
      letter-spacing: .03em;
      margin-top: 4px;
    }

    .auth-foot{
      text-align:center;
      color: var(--muted);
      font-size: 13px;
      padding: 0 22px 22px;
    }

    .auth-error{
      margin-top: 6px;
      color: #ffb3b3;
      font-size: 12px;
    }

    .auth-alert{
      background: rgba(255,42,42,.10);
      border: 1px solid rgba(255,42,42,.25);
      color: rgba(255,255,255,.85);
      border-radius: 12px;
      padding: 10px 12px;
      margin-bottom: 14px;
      font-size: 13px;
    }

    /* Botón ojito */
    .pw-btn{
      border:1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      color:#fff;
      border-radius: 12px;
      padding: 0 12px;
    }
    .pw-btn:hover{ background: rgba(255,255,255,.10); }

    /* Sección para datos de empresa */
    .auth-section{
      margin-top: 6px;
      padding: 12px;
      border-radius: 14px;
      border: 1px dashed rgba(255,255,255,.14);
      background: rgba(255,255,255,.03);
    }
    .auth-section-title{
      font-weight: 900;
      letter-spacing: .02em;
      margin: 0 0 10px;
      color: rgba(255,255,255,.88);
      font-size: 13px;
      text-transform: uppercase;
    }
  </style>
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <?php echo $__env->yieldContent('content'); ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mostrar/ocultar contraseñas
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-password-toggle]');
      if (!btn) return;

      const selector = btn.getAttribute('data-password-toggle');
      const input = selector ? document.querySelector(selector) : null;
      if (!input) return;

      const isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');
      btn.innerHTML = isPassword ? '🙈' : '👁️';
    });
  </script>
</body>
</html>
<?php /**PATH C:\laragon\www\concursos\resources\views/layouts/auth.blade.php ENDPATH**/ ?>