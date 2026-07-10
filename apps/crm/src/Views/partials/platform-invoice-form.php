<?php
$isEditingInvoice = isset($invoice) && is_array($invoice);
$invoiceValues = $isEditingInvoice ? $invoice : [
    'id' => '',
    'empresa_id' => $empresas[0]['id'] ?? '',
    'payment_id' => '',
    'invoice_series' => $nextInvoiceSeries ?? PlatformInvoiceRepository::defaultSeries(),
    'invoice_number' => $nextInvoiceNumber ?? PlatformInvoiceRepository::nextInvoiceNumber('M'),
    'issued_at' => date('Y-m-d'),
    'due_at' => date('Y-m-d', strtotime('+15 days')),
    'concept' => '',
    'taxable_base' => '0.00',
    'tax_rate' => '21.00',
    'payment_method' => 'TRANSFER',
    'status' => 'ISSUED',
    'notes' => '',
];
$invoiceStatusOptions = [
    'ISSUED' => 'Emitida',
    'SENT' => 'Enviada',
    'PAID' => 'Cobrada',
    'OVERDUE' => 'Vencida',
    'CANCELLED' => 'Cancelada',
];
$invoicePaymentMethods = [
    'TRANSFER' => 'Transferencia',
    'CARD' => 'Tarjeta',
    'STRIPE' => 'Stripe',
    'CASH' => 'Efectivo',
    'OTHER' => 'Otro',
];
?>

<form class="empresa-form" method="post">
  <input type="hidden" name="action" value="<?= $isEditingInvoice ? 'update_platform_invoice' : 'create_platform_invoice' ?>">
  <?php if ($isEditingInvoice): ?>
    <input type="hidden" name="id" value="<?= e($invoiceValues['id']) ?>">
  <?php endif; ?>

  <label class="field">
    <span>Empresa</span>
    <select name="empresa_id" required>
      <?php foreach ($empresas as $empresaOption): ?>
        <option value="<?= e($empresaOption['id']) ?>" <?= $invoiceValues['empresa_id'] === $empresaOption['id'] ? 'selected' : '' ?>>
          <?= e($empresaOption['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span>Pago asociado</span>
    <select name="payment_id">
      <option value="">Sin pago asociado</option>
      <?php foreach (($payments ?? []) as $paymentOption): ?>
        <option value="<?= e($paymentOption['id']) ?>" <?= ($invoiceValues['payment_id'] ?? '') === $paymentOption['id'] ? 'selected' : '' ?>>
          <?= e($paymentOption['empresa_name'] . ' - ' . $paymentOption['concept'] . ' - ' . money_amount($paymentOption['amount'])) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span>Serie</span>
    <input name="invoice_series" required maxlength="32" value="<?= e($invoiceValues['invoice_series']) ?>" placeholder="M">
  </label>
  <label class="field">
    <span>Numero</span>
    <input name="invoice_number" required type="number" min="1" step="1" value="<?= e((string) $invoiceValues['invoice_number']) ?>">
  </label>
  <label class="field">
    <span>Fecha</span>
    <input name="issued_at" required type="date" value="<?= e($invoiceValues['issued_at'] ? date('Y-m-d', strtotime($invoiceValues['issued_at'])) : date('Y-m-d')) ?>">
  </label>
  <label class="field">
    <span>Vencimiento</span>
    <input name="due_at" type="date" value="<?= e($invoiceValues['due_at'] ? date('Y-m-d', strtotime($invoiceValues['due_at'])) : '') ?>">
  </label>
  <label class="field form-full">
    <span>Concepto</span>
    <input name="concept" required value="<?= e($invoiceValues['concept']) ?>" placeholder="Suscripcion Membora CRM - julio 2026">
  </label>
  <label class="field">
    <span>Base imponible</span>
    <input name="taxable_base" inputmode="decimal" value="<?= e((string) $invoiceValues['taxable_base']) ?>" placeholder="89.00">
  </label>
  <label class="field">
    <span>IVA %</span>
    <input name="tax_rate" inputmode="decimal" value="<?= e((string) $invoiceValues['tax_rate']) ?>" placeholder="21.00">
  </label>
  <label class="field">
    <span>Forma de pago</span>
    <select name="payment_method">
      <?php foreach ($invoicePaymentMethods as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $invoiceValues['payment_method'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span>Estado</span>
    <select name="status">
      <?php foreach ($invoiceStatusOptions as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $invoiceValues['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field form-full">
    <span>Notas internas</span>
    <textarea name="notes" rows="4" placeholder="Datos fiscales pendientes, referencia de transferencia, servicio puntual..."><?= e($invoiceValues['notes']) ?></textarea>
  </label>

  <div class="form-actions form-full">
    <button class="secondary-action" type="button" data-close-modal>Cancelar</button>
    <button class="primary-action" type="submit"><?= $isEditingInvoice ? 'Guardar factura' : 'Crear factura' ?></button>
  </div>
</form>
