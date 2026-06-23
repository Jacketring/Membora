<?php

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/View.php';

Actions::handle();

$route = $_GET['route'] ?? 'dashboard';

if ($route === 'login') {
    if (Auth::user()) {
        redirect('dashboard');
    }

    render('login');
    exit;
}

Auth::requireUser();
$tenantId = Auth::tenantId();

switch ($route) {
    case 'dashboard':
        render_layout('Panel', 'dashboard', [
            'summary' => DashboardRepository::summary($tenantId),
        ]);
        break;

    case 'leads':
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'stage' => trim((string) ($_GET['stage'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to' => trim((string) ($_GET['date_to'] ?? '')),
        ];
        $leads = LeadRepository::all(
            $tenantId,
            $filters['q'],
            $filters['stage'],
            $filters['status'],
            $filters['date_from'],
            $filters['date_to']
        );
        render_layout('Leads', 'leads', [
            'filters' => $filters,
            'stages' => PipelineRepository::all($tenantId),
            'metrics' => LeadRepository::metrics($tenantId),
            'leads' => $leads,
            'leadNotes' => LeadRepository::notesByLeadIds($tenantId, array_column($leads, 'id')),
        ]);
        break;

    case 'tasks':
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
        ];
        render_layout('Tareas', 'tasks', [
            'filters' => $filters,
            'staff' => StaffRepository::all($tenantId),
            'metrics' => TaskRepository::metrics($tenantId),
            'tasks' => TaskRepository::all($tenantId, $filters['q'], $filters['status']),
        ]);
        break;

    default:
        redirect('dashboard');
}
