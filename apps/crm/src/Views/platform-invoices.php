<?php
$filters = $filters ?? ['q' => '', 'status' => ''];
$invoices = $invoices ?? [];
$empresas = $empresas ?? [];
$payments = $payments ?? [];
$metrics = $metrics ?? ['issued_month' => 0, 'pending_amount' => 0, 'paid_month' => 0, 'overdue' => 0];
$statusOptions = [
    '' => 'Todas',
    'ISSUED' => 'Emitida',
    'SENT' => 'Enviada',
    'PAID' => 'Cobrada',
    'OVERDUE' => 'Vencida',
    'CANCELLED' => 'Cancelada',
];
?>

<div class="page-heading leads-heading platform-heading">
  <div>
    <h2>Facturas CRM</h2>
    <p>Facturas emitidas por Membora a gimnasios cliente, con serie, numero, IVA y estado de cobro.</p>
  </div>
  <button class="primary-action" type="button" data-open-modal="invoice-create-modal">Nueva factura</button>
</div>

<section class="dashboard-metrics">
  <article class="dashboard-metric dashboard-metric--primary">
    <span>Emitido este mes</span>
    <strong><?= e(money_amount($metrics['issued_month'])) ?></strong>
    <small>Facturas no canceladas</small>
  </article>
  <article class="dashboard-metric dashboard-metric--orange">
    <span>Pendiente</span>
    <strong><?= e(money_amount($metrics['pending_amount'])) ?></strong>
    <small>Emitidas, enviadas o vencidas</small>
  </article>
  <article class="dashboard-metric dashboard-metric--green">
    <span>Cobrado este mes</span>
    <strong><?= e(money_amount($metrics['paid_month'])) ?></strong>
    <small>Facturas marcadas cobradas</small>
  </article>
  <article class="dashboard-metric dashboard-metric--danger">
    <span>Vencidas</span>
    <strong><?= (int) $metrics['overdue'] ?></strong>
    <small>Requieren seguimiento</small>
  </article>
</section>

<form class="lead-toolbar platform-toolbar platform-toolbar--payments" method="get" action="index.php" data-auto-filter-form>
  <input type="hidden" name="route" value="platform-invoices">
  <label class="field platform-search">
    <span>Buscar</span>
    <input name="q" value="<?= e($filters['q']) ?>" placeholder="Numero, empresa, concepto o notas" data-auto-submit-input>
  </label>
  <label class="field platform-filter-field">
    <span>Estado</span>
    <select name="status" data-auto-submit-input>
      <?php foreach ($statusOptions as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="primary-action" type="submit">Filtrar</button>
</form>

<section class="leads-table-card">
  <header>
    <div>
      <h3>Listado de facturas</h3>
      <span><?= count($invoices) ?> resultados</span>
    </div>
  </header>
  <div class="leads-table-wrap">
    <table class="leads-table platform-table platform-table--payments">
      <thead>
        <tr>
          <th>Factura</th>
          <th>Cliente</th>
          <th>Concepto</th>
          <th>Fecha</th>
          <th>Vencimiento</th>
          <th>Base</th>
          <th>IVA</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $invoice): ?>
          <?php $statusClass = strtolower((string) $invoice['status']); ?>
          <tr class="lead-data-row clickable-row" tabindex="0" data-open-modal="invoice-edit-<?= e($invoice['id']) ?>">
            <td><strong><?= e($invoice['invoice_code']) ?></strong></td>
            <td>
              <?= e($invoice['empresa_name']) ?>
              <span class="table-subtext"><?= e($invoice['contact_email'] ?: 'Sin contacto') ?></span>
            </td>
            <td><?= e($invoice['concept']) ?></td>
            <td><?= e(format_date_short($invoice['issued_at'])) ?></td>
            <td><?= e(format_date_short($invoice['due_at'])) ?></td>
            <td><?= e(money_amount($invoice['taxable_base'])) ?></td>
            <td><?= e(money_amount($invoice['tax_amount'])) ?></td>
            <td><strong><?= e(money_amount($invoice['total_amount'])) ?></strong></td>
            <td><span class="status-badge status-badge--<?= e($statusClass) ?>"><?= e(platform_invoice_status_label($invoice['status'])) ?></span></td>
            <td>
              <div class="platform-row-actions">
                <a class="support-invoice-action" href="index.php?route=platform-invoice&id=<?= urlencode($invoice['id']) ?>" target="_blank" rel="noopener" aria-label="Ver factura <?= e($invoice['invoice_code']) ?>">
                  <svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6V2Zm8 1.8V8h4.2L14 3.8ZM8 11h8v2H8v-2Zm0 4h8v2H8v-2Zm0 4h5v1H8v-1Z"/></svg>
                  <span>PDF</span>
                </a>
                <button class="support-edit-action" type="button" data-open-modal="invoice-edit-<?= e($invoice['id']) ?>" aria-label="Editar factura <?= e($invoice['invoice_code']) ?>">
                  <svg viewBox="0 0 24 24"><path d="M4 17.3V20h2.7L17.9 8.8l-2.7-2.7L4 17.3Zm15.8-10.6a1 1 0 0 0 0-1.4l-1.1-1.1a1 1 0 0 0-1.4 0l-.9.9 2.7 2.7.7-.8Z"/></svg>
                  <span>Editar</span>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$invoices): ?>
          <tr><td colspan="10" class="empty-state">No hay facturas que coincidan con los filtros actuales.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php unset($invoice); ?>
<dialog class="modal-card empresa-modal" id="invoice-create-modal">
  <header>
    <div>
      <h2>Nueva factura</h2>
      <p>Emite una factura manual con serie y numero sugerido.</p>
    </div>
    <button class="modal-close-action" type="button" data-close-modal aria-label="Cerrar">Cerrar</button>
  </header>
  <?php require __DIR__ . '/partials/platform-invoice-form.php'; ?>
</dialog>

<?php foreach ($invoices as $invoice): ?>
  <dialog class="modal-card empresa-modal" id="invoice-edit-<?= e($invoice['id']) ?>">
    <header>
      <div>
        <h2><?= e($invoice['invoice_code']) ?></h2>
        <p><?= e($invoice['empresa_name']) ?> - <?= e(platform_invoice_status_label($invoice['status'])) ?></p>
      </div>
      <button class="modal-close-action" type="button" data-close-modal aria-label="Cerrar">Cerrar</button>
    </header>
    <?php require __DIR__ . '/partials/platform-invoice-form.php'; ?>
  </dialog>
<?php endforeach; ?>
