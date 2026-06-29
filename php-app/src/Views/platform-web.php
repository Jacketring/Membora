<?php
$settings = $settings ?? ['target_empresa_id' => ''];
$empresas = $empresas ?? [];
$logs = $logs ?? [];
$targetEmpresa = $targetEmpresa ?? null;
$webhookUrl = $webhookUrl ?? app_base_url() . '/webhook/lead';
$webUrl = $webUrl ?? 'https://app.web.josehurtado.dev';
$connectedEmpresas = array_values(array_filter($empresas, static fn (array $empresa): bool => !empty($empresa['tenant_id'])));
?>

<div class="page-heading leads-heading platform-heading">
  <div>
    <h2>Web comercial</h2>
    <p>Configura la web publica de Membora y decide que empresa recibe los formularios de contacto.</p>
  </div>
  <a class="secondary-action" href="<?= e(rtrim((string) $webUrl, '/')) ?>" target="_blank" rel="noreferrer">Abrir web</a>
</div>

<section class="platform-admin-grid platform-admin-grid--web">
  <article class="platform-panel platform-panel--wide">
    <header>
      <div>
        <h3>Destino de los leads web</h3>
        <p>El formulario de la web oficial crea leads directamente en la empresa seleccionada.</p>
      </div>
      <span><?= $targetEmpresa ? 'Activo' : 'Pendiente' ?></span>
    </header>

    <?php if ($targetEmpresa): ?>
      <div class="web-target-card">
        <div>
          <strong><?= e($targetEmpresa['name']) ?></strong>
          <small>Empresa receptora actual · <?= e(empresa_status_label($targetEmpresa['status'])) ?></small>
        </div>
        <b><?= e($targetEmpresa['contact_email'] ?: 'Sin contacto') ?></b>
      </div>
    <?php else: ?>
      <p class="platform-empty">Selecciona una empresa con CRM conectado para empezar a recibir leads desde la web.</p>
    <?php endif; ?>

    <form class="empresa-form platform-web-form" method="post">
      <input type="hidden" name="action" value="update_platform_web_settings">
      <label class="field form-full">
        <span>Empresa que recibira los leads</span>
        <select name="empresa_id" required>
          <option value="">Seleccionar empresa</option>
          <?php foreach ($connectedEmpresas as $empresa): ?>
            <option value="<?= e($empresa['id']) ?>" <?= ($settings['target_empresa_id'] ?? '') === $empresa['id'] ? 'selected' : '' ?>>
              <?= e($empresa['name']) ?><?= $empresa['contact_email'] ? ' - ' . e($empresa['contact_email']) : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <div class="form-actions form-full">
        <button class="primary-action" type="submit">Guardar destino</button>
      </div>
    </form>
  </article>

  <article class="platform-panel">
    <header>
      <div>
        <h3>Conexion tecnica</h3>
        <p>No hay que copiar tokens en la web. Solo se acepta el dominio configurado.</p>
      </div>
    </header>
    <div class="web-info-list">
      <div>
        <span>Web publica</span>
        <strong><?= e(rtrim((string) $webUrl, '/')) ?></strong>
      </div>
      <div>
        <span>Webhook interno</span>
        <strong><?= e($webhookUrl) ?></strong>
      </div>
      <div>
        <span>Seguridad</span>
        <strong>Origen permitido + honeypot + rate limit</strong>
      </div>
    </div>
  </article>
</section>

<section class="leads-table-card">
  <header>
    <div>
      <h3>Ultimos formularios recibidos</h3>
      <span><?= count($logs) ?> registros</span>
    </div>
  </header>
  <div class="leads-table-wrap">
    <table class="leads-table platform-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Empresa</th>
          <th>Lead</th>
          <th>Estado</th>
          <th>Origen</th>
          <th>Detalle</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <?php
            $leadName = trim((string) (($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')));
            $status = (string) ($log['status'] ?? '');
          ?>
          <tr>
            <td><?= e(format_date($log['created_at'])) ?></td>
            <td><?= e($log['empresa_name'] ?? 'Sin empresa') ?></td>
            <td>
              <strong><?= e($leadName ?: 'Lead no creado') ?></strong>
              <span class="table-subtext"><?= e($log['email'] ?: ($log['phone'] ?: 'Sin contacto')) ?></span>
            </td>
            <td><span class="status-badge status-badge--<?= e($status === 'success' || $status === 'duplicate' ? 'active' : 'cancelled') ?>"><?= e(webhook_status_label($status)) ?></span></td>
            <td><?= e($log['source_url'] ?: 'Web publica') ?></td>
            <td><?= e($log['error_message'] ?: 'Recibido correctamente') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$logs): ?>
          <tr><td colspan="6" class="empty-state">Todavia no se han recibido formularios desde la web.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
