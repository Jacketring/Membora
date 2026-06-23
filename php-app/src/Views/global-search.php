<?php
$totalResults = array_sum(array_map('count', $results));
?>

<div class="page-heading">
  <div>
    <h2>Busqueda global</h2>
    <p>Busca tareas, socios, clases, membresias y leads desde un unico sitio.</p>
  </div>
</div>

<form class="global-search-page-form" method="get">
  <input type="hidden" name="route" value="search">
  <input name="q" value="<?= e($query) ?>" placeholder="Buscar por nombre, telefono, email, tarea, clase o membresia..." autofocus>
  <button class="primary-action primary-action--compact" type="submit">Buscar</button>
</form>

<?php if ($query === ''): ?>
  <section class="search-empty-panel">
    <h3>Empieza escribiendo algo</h3>
    <p>Por ejemplo: nombre de un socio, telefono, email, tarea pendiente, tipo de clase o plan de membresia.</p>
  </section>
<?php else: ?>
  <div class="search-summary">
    <strong><?= (int) $totalResults ?></strong>
    <span>resultados para "<?= e($query) ?>"</span>
  </div>

  <section class="global-results-grid">
    <article class="search-result-card">
      <header>
        <div>
          <h3>Leads</h3>
          <p>Oportunidades comerciales encontradas.</p>
        </div>
        <a href="index.php?route=leads&q=<?= urlencode($query) ?>">Ver todos</a>
      </header>
      <div class="search-result-list">
        <?php foreach ($results['leads'] as $lead): ?>
          <a href="index.php?route=leads&q=<?= urlencode($query) ?>" class="search-result-item">
            <span class="result-icon result-icon--lead">L</span>
            <div>
              <strong><?= e(trim($lead['first_name'] . ' ' . ($lead['last_name'] ?? ''))) ?></strong>
              <small><?= e($lead['email'] ?: ($lead['phone'] ?: $lead['interest'])) ?></small>
            </div>
            <em class="status-badge status-badge--<?= e(strtolower($lead['status'])) ?>"><?= e(status_label($lead['status'])) ?></em>
          </a>
        <?php endforeach; ?>
        <?php if (!$results['leads']): ?>
          <p class="empty-state">No hay leads encontrados.</p>
        <?php endif; ?>
      </div>
    </article>

    <article class="search-result-card">
      <header>
        <div>
          <h3>Tareas</h3>
          <p>Seguimientos y actividades relacionadas.</p>
        </div>
        <a href="index.php?route=tasks&q=<?= urlencode($query) ?>">Ver todas</a>
      </header>
      <div class="search-result-list">
        <?php foreach ($results['tasks'] as $task): ?>
          <a href="index.php?route=tasks&q=<?= urlencode($query) ?>" class="search-result-item">
            <span class="result-icon result-icon--task">T</span>
            <div>
              <strong><?= e($task['title']) ?></strong>
              <small><?= e(format_date($task['due_at'])) ?> - <?= e($task['assigned_name'] ?: 'Sin responsable') ?></small>
            </div>
            <em class="status-badge status-badge--<?= e(strtolower($task['status'])) ?>"><?= e(status_label($task['status'])) ?></em>
          </a>
        <?php endforeach; ?>
        <?php if (!$results['tasks']): ?>
          <p class="empty-state">No hay tareas encontradas.</p>
        <?php endif; ?>
      </div>
    </article>

    <article class="search-result-card">
      <header>
        <div>
          <h3>Socios</h3>
          <p>Personas dadas de alta en el centro.</p>
        </div>
        <span class="module-pill">Modulo pendiente</span>
      </header>
      <div class="search-result-list">
        <?php foreach ($results['members'] as $member): ?>
          <div class="search-result-item">
            <span class="result-icon result-icon--member">S</span>
            <div>
              <strong><?= e(trim($member['first_name'] . ' ' . ($member['last_name'] ?? ''))) ?></strong>
              <small><?= e($member['email'] ?: ($member['phone'] ?: 'Sin contacto')) ?></small>
            </div>
            <em class="status-badge status-badge--<?= e(strtolower($member['status'])) ?>"><?= e(status_label($member['status'])) ?></em>
          </div>
        <?php endforeach; ?>
        <?php if (!$results['members']): ?>
          <p class="empty-state">No hay socios encontrados.</p>
        <?php endif; ?>
      </div>
    </article>

    <article class="search-result-card">
      <header>
        <div>
          <h3>Membresias</h3>
          <p>Planes y productos comerciales.</p>
        </div>
        <span class="module-pill">Modulo pendiente</span>
      </header>
      <div class="search-result-list">
        <?php foreach ($results['memberships'] as $plan): ?>
          <div class="search-result-item">
            <span class="result-icon result-icon--membership">M</span>
            <div>
              <strong><?= e($plan['name']) ?></strong>
              <small><?= e($plan['description'] ?: ('Precio: ' . ($plan['price'] ?? '-'))) ?></small>
            </div>
            <em><?= e($plan['status'] ?? '') ?></em>
          </div>
        <?php endforeach; ?>
        <?php if (!$results['memberships']): ?>
          <p class="empty-state">No hay membresias encontradas.</p>
        <?php endif; ?>
      </div>
    </article>

    <article class="search-result-card">
      <header>
        <div>
          <h3>Clases</h3>
          <p>Tipos de clase disponibles.</p>
        </div>
        <span class="module-pill">Modulo pendiente</span>
      </header>
      <div class="search-result-list">
        <?php foreach ($results['classes'] as $class): ?>
          <div class="search-result-item">
            <span class="result-icon result-icon--class">C</span>
            <div>
              <strong><?= e($class['name']) ?></strong>
              <small><?= e($class['description'] ?: ('Capacidad: ' . ($class['capacity'] ?? '-'))) ?></small>
            </div>
            <em><?= e($class['status'] ?? '') ?></em>
          </div>
        <?php endforeach; ?>
        <?php if (!$results['classes']): ?>
          <p class="empty-state">No hay clases encontradas.</p>
        <?php endif; ?>
      </div>
    </article>
  </section>
<?php endif; ?>
