<?php
$cssPath = __DIR__ . '/../../public/assets/app.css';
$cssVersion = is_file($cssPath) ? (string) filemtime($cssPath) : '1';
$empresa = $empresa ?? null;
$companyName = $empresa['name'] ?? 'Membora CRM';
$primaryColor = hex_color_or_default($empresa['primary_color'] ?? '#0754d6');
$isConnected = $empresa && !empty($empresa['tenant_id']) && in_array((string) ($empresa['status'] ?? ''), ['ACTIVE', 'TRIAL'], true);
$leadCountText = $isConnected ? 'Respuesta desde el CRM del centro' : 'Pagina pendiente de conectar al CRM';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="<?= e($primaryColor) ?>">
  <title><?= e($companyName) ?> - Gestion fitness y captacion</title>
  <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
  <link rel="stylesheet" href="assets/app.css?v=<?= e($cssVersion) ?>">
  <style>:root { --primary: <?= e($primaryColor) ?>; }</style>
</head>
<body class="public-site-body">
  <header class="public-site-nav">
    <a class="public-site-brand" href="#inicio" aria-label="<?= e($companyName) ?>">
      <span class="brand-icon">M</span>
      <span>
        <strong><?= e($companyName) ?></strong>
        <small>Centro fitness conectado con Membora CRM</small>
      </span>
    </a>
    <nav aria-label="Navegacion publica">
      <a href="#servicios">Servicios</a>
      <a href="#metodo">Metodo</a>
      <a href="#contacto">Contacto</a>
    </nav>
  </header>

  <main id="inicio">
    <section class="public-hero">
      <div class="public-hero-media" aria-hidden="true"></div>
      <div class="public-hero-content">
        <p class="public-eyebrow">Entrena con seguimiento real</p>
        <h1><?= e($companyName) ?></h1>
        <p>Gestionamos tus objetivos, pruebas y seguimiento desde un CRM pensado para centros fitness. Deja tus datos y el equipo comercial te contactara desde el panel de leads.</p>
        <div class="public-hero-actions">
          <a class="public-primary-link" href="#contacto">Solicitar informacion</a>
          <a class="public-secondary-link" href="#servicios">Ver servicios</a>
        </div>
      </div>
    </section>

    <section class="public-proof" aria-label="Resumen del centro">
      <article>
        <strong>Pruebas</strong>
        <span>Agenda una primera visita o clase de prueba.</span>
      </article>
      <article>
        <strong>Seguimiento</strong>
        <span>El equipo centraliza tus datos y proxima accion.</span>
      </article>
      <article>
        <strong><?= e($leadCountText) ?></strong>
        <span>Tu solicitud entra directamente en Leads.</span>
      </article>
    </section>

    <section class="public-section" id="servicios">
      <div class="public-section-heading">
        <span>Servicios</span>
        <h2>Una experiencia clara desde el primer contacto</h2>
      </div>
      <div class="public-card-grid">
        <article>
          <h3>Alta y prueba</h3>
          <p>Solicitud inicial, contacto comercial y seguimiento hasta convertir el lead en socio.</p>
        </article>
        <article>
          <h3>Clases y reservas</h3>
          <p>Sesiones por calendario, aforo y reservas para organizar la actividad diaria del centro.</p>
        </article>
        <article>
          <h3>Membresias</h3>
          <p>Planes semanales, mensuales o anuales con caducidad visible para recepcion y administracion.</p>
        </article>
      </div>
    </section>

    <section class="public-split" id="metodo">
      <div>
        <span class="public-eyebrow">Metodo de trabajo</span>
        <h2>Del formulario al CRM sin perder oportunidades</h2>
        <p>Cuando una persona solicita informacion, Membora crea o actualiza el lead del centro, evita duplicados y deja una nota con el mensaje recibido.</p>
      </div>
      <ol class="public-steps">
        <li><strong>1</strong><span>El interesado rellena el formulario.</span></li>
        <li><strong>2</strong><span>El lead entra en el pipeline comercial.</span></li>
        <li><strong>3</strong><span>El equipo agenda seguimiento o prueba.</span></li>
      </ol>
    </section>

    <section class="public-contact-section" id="contacto">
      <div class="public-contact-copy">
        <span class="public-eyebrow">Contacto</span>
        <h2>Solicita informacion</h2>
        <p>Completa el formulario y la solicitud quedara registrada en el CRM de <?= e($companyName) ?>.</p>
        <?php if (!$isConnected): ?>
          <div class="public-alert public-alert--error">Esta pagina aun no esta conectada a una empresa activa con CRM.</div>
        <?php endif; ?>
        <?php if ($flash): ?>
          <div class="public-alert <?= ($flash['type'] ?? '') === 'error' ? 'public-alert--error' : 'public-alert--success' ?>">
            <?= e($flash['message']) ?>
          </div>
        <?php endif; ?>
      </div>
      <form class="public-lead-form" method="post" action="index.php?route=web&empresa=<?= urlencode((string) ($empresa['id'] ?? '')) ?>#contacto">
        <input type="hidden" name="action" value="public_web_lead">
        <input type="hidden" name="empresa_id" value="<?= e($empresa['id'] ?? '') ?>">
        <input type="hidden" name="utm_source" value="pagina_publica">
        <input type="hidden" name="utm_medium" value="web">
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="public-honeypot" aria-hidden="true">
        <label>
          <span>Nombre</span>
          <input name="nombre" required autocomplete="given-name" placeholder="Tu nombre">
        </label>
        <label>
          <span>Apellidos</span>
          <input name="apellidos" autocomplete="family-name" placeholder="Tus apellidos">
        </label>
        <label>
          <span>Email</span>
          <input name="email" type="email" autocomplete="email" placeholder="tu@email.com">
        </label>
        <label>
          <span>Telefono</span>
          <input name="telefono" inputmode="tel" autocomplete="tel" placeholder="+34 600 000 000">
        </label>
        <label class="public-field-wide">
          <span>Que necesitas?</span>
          <textarea name="mensaje" rows="4" placeholder="Quiero informacion sobre pruebas, clases o membresias."></textarea>
        </label>
        <label class="public-check public-field-wide">
          <input name="acepta_rgpd" value="1" type="checkbox" required>
          <span>Acepto que el centro contacte conmigo para gestionar esta solicitud.</span>
        </label>
        <button class="public-submit" type="submit" <?= $isConnected ? '' : 'disabled' ?>>Enviar solicitud</button>
      </form>
    </section>
  </main>

  <footer class="public-site-footer">
    <span><?= e($companyName) ?></span>
    <span>Captacion gestionada con Membora CRM</span>
  </footer>
</body>
</html>
