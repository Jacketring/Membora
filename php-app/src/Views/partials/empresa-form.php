<?php
$isEditingEmpresa = isset($empresa) && is_array($empresa);
$empresaValues = $isEditingEmpresa ? $empresa : [
    'id' => '',
    'name' => '',
    'contact_email' => '',
    'plan' => 'BASIC',
    'status' => 'TRIAL',
    'payment_status' => 'TRIAL',
    'monthly_price' => '0.00',
    'next_payment_at' => '',
    'notes' => '',
];
?>

<form class="form-grid" method="post">
  <input type="hidden" name="action" value="<?= $isEditingEmpresa ? 'update_empresa' : 'create_empresa' ?>">
  <?php if ($isEditingEmpresa): ?>
    <input type="hidden" name="id" value="<?= e($empresaValues['id']) ?>">
  <?php endif; ?>

  <label>
    Empresa
    <input name="name" required value="<?= e($empresaValues['name']) ?>" placeholder="NexoFit Studio">
  </label>
  <label>
    Email de contacto
    <input name="contact_email" type="email" value="<?= e($empresaValues['contact_email']) ?>" placeholder="admin@empresa.com">
  </label>
  <label>
    Plan
    <select name="plan">
      <?php foreach ($planOptions as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $empresaValues['plan'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>
    Estado CRM
    <select name="status">
      <?php foreach (array_filter($statusOptions, static fn ($label, $value): bool => $value !== '', ARRAY_FILTER_USE_BOTH) as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $empresaValues['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>
    Estado de pago
    <select name="payment_status">
      <?php foreach (array_filter($paymentOptions, static fn ($label, $value): bool => $value !== '', ARRAY_FILTER_USE_BOTH) as $value => $label): ?>
        <option value="<?= e($value) ?>" <?= $empresaValues['payment_status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>
    Precio mensual
    <input name="monthly_price" inputmode="decimal" value="<?= e((string) $empresaValues['monthly_price']) ?>" placeholder="49.00">
  </label>
  <label>
    Proximo pago
    <input name="next_payment_at" type="date" value="<?= e($empresaValues['next_payment_at'] ? date('Y-m-d', strtotime($empresaValues['next_payment_at'])) : '') ?>">
  </label>
  <label class="form-full">
    Notas internas
    <textarea name="notes" rows="4" placeholder="Contrato, incidencias de pago, contacto decisor..."><?= e($empresaValues['notes']) ?></textarea>
  </label>

  <div class="form-actions form-full">
    <button class="secondary-action" type="button" data-close-modal>Cancelar</button>
    <button class="primary-action" type="submit"><?= $isEditingEmpresa ? 'Guardar empresa' : 'Crear empresa' ?></button>
  </div>
</form>
