<div class="page-heading">
  <div>
    <h2>Leads</h2>
    <p>Gestiona oportunidades comerciales desde el primer contacto hasta el alta como socio.</p>
  </div>
  <button class="primary-action primary-action--compact" data-open-modal="lead-modal" type="button">Nuevo lead</button>
</div>

<section class="lead-metrics">
  <article class="lead-metric lead-metric--blue">
    <span>Abiertos</span>
    <strong><?= (int) $metrics['open'] ?></strong>
  </article>
  <article class="lead-metric lead-metric--green">
    <span>Convertidos</span>
    <strong><?= (int) $metrics['converted'] ?></strong>
  </article>
  <article class="lead-metric lead-metric--orange">
    <span>Perdidos</span>
    <strong><?= (int) $metrics['lost'] ?></strong>
  </article>
  <article class="lead-metric lead-metric--dark">
    <span>Conversion</span>
    <strong><?= (int) $metrics['conversion'] ?>%</strong>
  </article>
</section>

<form class="lead-toolbar" method="get">
  <input type="hidden" name="route" value="leads">
  <div class="lead-search">
    <span>Buscar</span>
    <input name="q" value="<?= e($filters['q']) ?>" placeholder="Nombre, telefono, email o interes">
  </div>
  <div class="lead-filter-group">
    <select name="stage">
      <option value="">Todas las etapas</option>
      <?php foreach ($stages as $stage): ?>
        <option value="<?= e($stage['id']) ?>" <?= $filters['stage'] === $stage['id'] ? 'selected' : '' ?>>
          <?= e($stage['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <select name="status">
      <option value="">Todos los estados</option>
      <option value="OPEN" <?= $filters['status'] === 'OPEN' ? 'selected' : '' ?>>Abiertos</option>
      <option value="CONVERTED" <?= $filters['status'] === 'CONVERTED' ? 'selected' : '' ?>>Convertidos</option>
      <option value="LOST" <?= $filters['status'] === 'LOST' ? 'selected' : '' ?>>Perdidos</option>
    </select>
    <input class="date-filter" name="date_from" type="date" value="<?= e($filters['date_from']) ?>" aria-label="Fecha desde">
    <input class="date-filter" name="date_to" type="date" value="<?= e($filters['date_to']) ?>" aria-label="Fecha hasta">
  </div>
  <button class="primary-action primary-action--compact" type="submit">Filtrar</button>
</form>

<section class="leads-table-card">
  <header>
    <div>
      <h3>Listado de leads</h3>
      <span><?= count($leads) ?> resultados</span>
    </div>
  </header>

  <div class="leads-table-wrap">
    <table class="leads-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Telefono</th>
          <th>Email</th>
          <th>Origen</th>
          <th>Interes</th>
          <th>Etapa</th>
          <th>Estado</th>
          <th>Responsable</th>
          <th>Creacion</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($leads as $lead): ?>
          <tr class="lead-data-row">
            <td>
              <strong><?= e(trim($lead['first_name'] . ' ' . ($lead['last_name'] ?? ''))) ?></strong>
            </td>
            <td><?= e($lead['phone'] ?: 'Sin telefono') ?></td>
            <td><?= e($lead['email'] ?: 'Sin email') ?></td>
            <td><span class="source-badge"><?= e(source_label($lead['source'])) ?></span></td>
            <td><?= e($lead['interest'] ?: 'Sin interes') ?></td>
            <td>
              <form method="post" class="inline-form">
                <input type="hidden" name="action" value="update_lead_stage">
                <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
                <div class="stage-picker">
                  <span class="stage-dot"></span>
                  <select class="stage-select stage-select--table" name="pipeline_stage_id" onchange="this.form.submit()">
                    <?php foreach ($stages as $stage): ?>
                      <option value="<?= e($stage['id']) ?>" <?= $lead['pipeline_stage_id'] === $stage['id'] ? 'selected' : '' ?>>
                        <?= e($stage['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </form>
            </td>
            <td>
              <span class="status-badge status-badge--<?= e(strtolower($lead['status'])) ?>">
                <?= e(status_label($lead['status'])) ?>
              </span>
            </td>
            <td><?= e($lead['assigned_name'] ?: 'Sin asignar') ?></td>
            <td><?= e(format_date($lead['created_at'])) ?></td>
            <td>
              <div class="row-actions">
                <button class="icon-action" data-open-modal="lead-detail-<?= e($lead['id']) ?>" type="button" title="Editar y ver detalles">Editar</button>
                <?php if ($lead['status'] !== 'CONVERTED'): ?>
                  <form method="post">
                    <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
                    <button name="action" value="convert_lead">Convertir</button>
                  </form>
                <?php endif; ?>
                <form method="post" onsubmit="return confirm('Eliminar este lead?')">
                  <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
                  <button class="icon-action danger-action" name="action" value="delete_lead" title="Eliminar lead">&#128465;</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (!$leads): ?>
          <tr>
            <td class="leads-empty-cell" colspan="10">No hay leads que coincidan con los filtros actuales.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php foreach ($leads as $lead): ?>
  <?php $notes = $leadNotes[$lead['id']] ?? []; ?>
  <dialog id="lead-detail-<?= e($lead['id']) ?>" class="modal-card lead-detail-modal">
    <header>
      <div>
        <h2><?= e(trim($lead['first_name'] . ' ' . ($lead['last_name'] ?? ''))) ?></h2>
        <p><?= e($lead['phone'] ?: 'Sin telefono') ?> · <?= e($lead['email'] ?: 'Sin email') ?></p>
      </div>
      <button data-close-modal type="button">Cerrar</button>
    </header>

    <form method="post">
      <input type="hidden" name="action" value="update_lead">
      <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
      <div class="form-grid">
        <label class="field">
          <span>Nombre</span>
          <input name="first_name" required value="<?= e($lead['first_name']) ?>">
        </label>
        <label class="field">
          <span>Apellidos</span>
          <input name="last_name" value="<?= e($lead['last_name']) ?>">
        </label>
        <label class="field">
          <span>Telefono</span>
          <input name="phone" value="<?= e($lead['phone']) ?>">
        </label>
        <label class="field">
          <span>Email</span>
          <input name="email" type="email" value="<?= e($lead['email']) ?>">
        </label>
        <label class="field">
          <span>Origen</span>
          <select name="source">
            <?php foreach (['WALK_IN', 'WEBSITE', 'PHONE', 'SOCIAL_MEDIA', 'REFERRAL', 'OTHER'] as $source): ?>
              <option value="<?= e($source) ?>" <?= $lead['source'] === $source ? 'selected' : '' ?>><?= e(source_label($source)) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="field">
          <span>Etapa</span>
          <select name="pipeline_stage_id">
            <?php foreach ($stages as $stage): ?>
              <option value="<?= e($stage['id']) ?>" <?= $lead['pipeline_stage_id'] === $stage['id'] ? 'selected' : '' ?>><?= e($stage['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="field">
          <span>Proxima accion</span>
          <input name="next_action_at" type="datetime-local" value="<?= $lead['next_action_at'] ? e(date('Y-m-d\TH:i', strtotime($lead['next_action_at']))) : '' ?>">
        </label>
        <label class="field field--wide">
          <span>Interes principal</span>
          <input name="interest" value="<?= e($lead['interest']) ?>">
        </label>
      </div>
      <button class="primary-action" type="submit">Guardar cambios</button>
    </form>

    <section class="notes-panel">
      <h3>Notas internas</h3>
      <form method="post" class="note-form">
        <input type="hidden" name="action" value="add_lead_note">
        <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
        <label class="field">
          <span>Nueva nota</span>
          <textarea name="note" rows="3" placeholder="Escribe una nota sobre la llamada, visita o seguimiento..." required></textarea>
        </label>
        <button class="primary-action primary-action--compact" type="submit">Anadir nota</button>
      </form>

      <div class="notes-list">
        <?php foreach ($notes as $note): ?>
          <article>
            <p><?= nl2br(e($note['note'])) ?></p>
            <span><?= e($note['user_name'] ?: 'Usuario') ?> · <?= e(format_date($note['created_at'])) ?></span>
          </article>
        <?php endforeach; ?>
        <?php if (!$notes): ?>
          <p class="empty-note">Todavia no hay notas para este lead.</p>
        <?php endif; ?>
      </div>
    </section>
  </dialog>
<?php endforeach; ?>

<dialog id="lead-modal" class="modal-card">
  <form method="post">
    <input type="hidden" name="action" value="create_lead">
    <header>
      <h2>Nuevo lead</h2>
      <button data-close-modal type="button">Cerrar</button>
    </header>
    <div class="form-grid">
      <label class="field">
        <span>Nombre</span>
        <input name="first_name" required>
      </label>
      <label class="field">
        <span>Apellidos</span>
        <input name="last_name">
      </label>
      <label class="field">
        <span>Telefono</span>
        <input name="phone">
      </label>
      <label class="field">
        <span>Email</span>
        <input name="email" type="email">
      </label>
      <label class="field">
        <span>Origen</span>
        <select name="source">
          <option value="WALK_IN">Visita presencial</option>
          <option value="WEBSITE">Web</option>
          <option value="PHONE">Telefono</option>
          <option value="SOCIAL_MEDIA">Redes sociales</option>
          <option value="REFERRAL">Recomendacion</option>
          <option value="OTHER">Otro</option>
        </select>
      </label>
      <label class="field">
        <span>Etapa inicial</span>
        <select name="pipeline_stage_id">
          <?php foreach ($stages as $stage): ?>
            <option value="<?= e($stage['id']) ?>"><?= e($stage['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="field field--wide">
        <span>Interes principal</span>
        <input name="interest" placeholder="Ej. Prueba de HIIT, plan premium, bono mensual">
      </label>
    </div>
    <button class="primary-action" type="submit">Crear lead</button>
  </form>
</dialog>
