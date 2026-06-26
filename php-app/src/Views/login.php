<?php
$flash = flash();
$platformAdminEmail = EmpresaRepository::PLATFORM_ADMIN_EMAIL;
$platformAdminPassword = EmpresaRepository::PLATFORM_ADMIN_PASSWORD;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Membora CRM - Login</title>
  <link rel="stylesheet" href="assets/app.css">
</head>
<body>
  <main class="login-screen">
    <div class="login-overlay"></div>
    <section class="login-panel">
      <div class="brand-lockup brand-lockup--login">
        <div class="brand-icon">M</div>
        <div>
          <h1>Membora CRM</h1>
          <p>Portal de gestion fitness</p>
        </div>
      </div>
      <form class="login-card" method="post">
        <input type="hidden" name="action" value="login">
        <header>
          <h2>Accede a tu CRM</h2>
          <p>Introduce tus credenciales para gestionar tu centro.</p>
        </header>
        <?php if ($flash): ?>
          <div class="notice <?= $flash['type'] === 'error' ? 'notice-error' : 'notice-success' ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <label class="field">
          <span>Email</span>
          <div class="input-shell"><input name="email" type="email" required value="<?= e($platformAdminEmail) ?>" data-login-email></div>
        </label>
        <label class="field">
          <span>Contrasena</span>
          <div class="input-shell"><input name="password" type="password" required value="<?= e($platformAdminPassword) ?>" data-login-password></div>
        </label>
        <button class="primary-action" type="submit">Iniciar sesion</button>
        <div class="demo-note demo-note--platform">
          <div>
            <strong>Administrador de empresas</strong>
            <span>Email: <?= e($platformAdminEmail) ?></span>
            <span>Contrasena: <?= e($platformAdminPassword) ?></span>
          </div>
          <button
            class="demo-fill-action"
            type="button"
            data-fill-login
            data-email="<?= e($platformAdminEmail) ?>"
            data-password="<?= e($platformAdminPassword) ?>"
          >
            Usar estas credenciales
          </button>
        </div>
      </form>
    </section>
  </main>
  <script>
    document.querySelector('[data-fill-login]')?.addEventListener('click', (event) => {
      const trigger = event.currentTarget;
      const emailInput = document.querySelector('[data-login-email]');
      const passwordInput = document.querySelector('[data-login-password]');
      if (emailInput) emailInput.value = trigger.dataset.email || '';
      if (passwordInput) passwordInput.value = trigger.dataset.password || '';
      emailInput?.focus();
    });
  </script>
</body>
</html>
