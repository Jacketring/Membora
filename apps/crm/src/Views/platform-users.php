<?php
$filters = $filters ?? ['q' => '', 'status' => ''];
$users = $users ?? [];
$metrics = $metrics ?? ['active' => 0, 'inactive' => 0, 'with_login' => 0, 'total' => 0];
$statusOptions = ['' => 'Todos', 'ACTIVE' => 'Activos', 'INACTIVE' => 'Inactivos'];
?>

<div class="page-heading leads-heading platform-heading">
  <div>
    <h2>Usuarios Admin</h2>
    <p>Gestiona las cuentas con acceso a la administración general de Membora.</p>
  </div>
  <button class="primary-action" type="button" data-open-modal="platform-user-create-modal">Nuevo administrador</button>
</div>

<section class="dashboard-metrics" aria-label="Resumen de usuarios administradores">
  <article class="dashboard-metric dashboard-metric--green"><span>Activos</span><strong><?= (int) $metrics['active'] ?></strong><small>Pueden iniciar sesión</small></article>
  <article class="dashboard-metric dashboard-metric--orange"><span>Inactivos</span><strong><?= (int) $metrics['inactive'] ?></strong><small>Acceso deshabilitado</small></article>
  <article class="dashboard-metric dashboard-metric--primary"><span>Con accesos</span><strong><?= (int) $metrics['with_login'] ?></strong><small>Han iniciado sesión</small></article>
  <article class="dashboard-metric dashboard-metric--danger"><span>Total</span><strong><?= (int) $metrics['total'] ?></strong><small>Superadministradores</small></article>
</section>

<form class="lead-toolbar platform-toolbar platform-toolbar--payments" method="get" action="index.php" data-auto-filter-form>
  <input type="hidden" name="route" value="platform-users">
  <label class="field platform-search"><span>Buscar</span><input name="q" value="<?= e($filters['q']) ?>" placeholder="Nombre o email" data-auto-submit-input></label>
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
  <header><div><h3>Administradores de Membora</h3><span><?= count($users) ?> resultados</span></div></header>
  <div class="leads-table-wrap">
    <table class="leads-table users-table">
      <caption class="sr-only">Usuarios con acceso a la administración general de Membora</caption>
      <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th>Creación</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($users as $adminUser): ?>
          <tr>
            <td><div class="member-identity"><span class="member-avatar member-avatar--initials" aria-hidden="true"><?= e(initials($adminUser['name'])) ?></span><strong><?= e($adminUser['name']) ?></strong></div></td>
            <td><?= e($adminUser['email']) ?></td>
            <td><span class="source-badge"><?= e(role_label($adminUser['role_key'])) ?></span></td>
            <td><span class="status-badge status-badge--<?= e(strtolower($adminUser['status'])) ?>"><?= e(status_label($adminUser['status'])) ?></span></td>
            <td><?= e(format_date($adminUser['last_login_at'])) ?></td>
            <td><?= e(format_date($adminUser['created_at'])) ?></td>
            <td>
              <div class="row-actions">
                <button class="icon-action" type="button" data-open-modal="platform-user-edit-<?= e($adminUser['id']) ?>" title="Editar administrador" aria-label="Editar administrador <?= e($adminUser['name']) ?>">
                  <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 20h4.8L19.4 9.4a2.1 2.1 0 0 0 0-3L17.6 4.6a2.1 2.1 0 0 0-3 0L4 15.2V20Zm2-2v-1.95l7.25-7.25 1.95 1.95L7.95 18H6Zm10.6-8.65L14.65 7.4 16 6.05 17.95 8l-1.35 1.35Z"/></svg>
                </button>
                <?php if ($adminUser['id'] !== ($user['id'] ?? null)): ?>
                  <form method="post" data-confirm-message="¿Eliminar este administrador? Esta acción no se puede deshacer." data-confirm-action-label="Eliminar">
                    <input type="hidden" name="action" value="delete_platform_user">
                    <input type="hidden" name="id" value="<?= e($adminUser['id']) ?>">
                    <button class="icon-action danger-action" type="submit" title="Eliminar administrador" aria-label="Eliminar administrador <?= e($adminUser['name']) ?>">
                      <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M7 21a2 2 0 0 1-2-2V8h14v11a2 2 0 0 1-2 2H7ZM9 6V4h6v2h5v2H4V6h5Zm0 5v7h2v-7H9Zm4 0v7h2v-7h-2Z"/></svg>
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?><tr><td class="leads-empty-cell" colspan="7">No hay administradores que coincidan con los filtros actuales.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php foreach ($users as $adminUser): ?>
  <dialog class="modal-card" id="platform-user-edit-<?= e($adminUser['id']) ?>" aria-labelledby="platform-user-edit-title-<?= e($adminUser['id']) ?>">
    <form method="post" data-prevent-double-submit>
      <input type="hidden" name="action" value="update_platform_user">
      <input type="hidden" name="id" value="<?= e($adminUser['id']) ?>">
      <header>
        <div><h2 id="platform-user-edit-title-<?= e($adminUser['id']) ?>">Editar administrador</h2><p><?= e($adminUser['email']) ?></p></div>
        <button data-close-modal type="button">Cerrar</button>
      </header>
      <div class="form-grid">
        <label class="field"><span>Nombre</span><input name="name" required autocomplete="name" value="<?= e($adminUser['name']) ?>"></label>
        <label class="field"><span>Email</span><input name="email" type="email" required autocomplete="email" value="<?= e($adminUser['email']) ?>"></label>
        <label class="field"><span>Nueva contraseña</span><input name="password" type="password" minlength="12" autocomplete="new-password" placeholder="Dejar vacío para mantener la actual"></label>
        <label class="field">
          <span>Estado</span>
          <select name="status" <?= $adminUser['id'] === ($user['id'] ?? null) ? 'disabled' : '' ?>>
            <option value="ACTIVE" <?= $adminUser['status'] === 'ACTIVE' ? 'selected' : '' ?>>Activo</option>
            <option value="INACTIVE" <?= $adminUser['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactivo</option>
          </select>
          <?php if ($adminUser['id'] === ($user['id'] ?? null)): ?><input type="hidden" name="status" value="ACTIVE"><?php endif; ?>
        </label>
      </div>
      <button class="primary-action" type="submit">Guardar cambios</button>
    </form>
  </dialog>
<?php endforeach; ?>

<dialog class="modal-card" id="platform-user-create-modal" aria-labelledby="platform-user-create-title">
  <form method="post" data-prevent-double-submit>
    <input type="hidden" name="action" value="create_platform_user">
    <header><div><h2 id="platform-user-create-title">Nuevo administrador</h2><p>Esta cuenta tendrá acceso completo a la administración general de Membora.</p></div><button data-close-modal type="button">Cerrar</button></header>
    <div class="form-grid">
      <label class="field"><span>Nombre</span><input name="name" required autocomplete="name" placeholder="Ej. Ana García"></label>
      <label class="field"><span>Email</span><input name="email" type="email" required autocomplete="email" placeholder="ana@membora.es"></label>
      <label class="field"><span>Contraseña</span><input name="password" type="password" required minlength="12" autocomplete="new-password" placeholder="Mínimo 12 caracteres"></label>
      <label class="field"><span>Estado</span><select name="status"><option value="ACTIVE">Activo</option><option value="INACTIVE">Inactivo</option></select></label>
      <div class="field field--wide"><span>Rol asignado</span><p><span class="source-badge">Superadministrador</span></p></div>
    </div>
    <button class="primary-action" type="submit">Crear administrador</button>
  </form>
</dialog>
