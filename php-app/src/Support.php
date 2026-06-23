<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $route): never
{
    header('Location: index.php?route=' . urlencode($route));
    exit;
}

function post_value(string $key, ?string $default = null): ?string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function cuid(): string
{
    return 'php_' . bin2hex(random_bytes(12));
}

function format_date(?string $value): string
{
    if (!$value) {
        return 'Sin fecha';
    }

    $timestamp = strtotime($value);
    return $timestamp ? date('d/m/Y H:i', $timestamp) : 'Sin fecha';
}

function enum_label(string $value, array $labels): string
{
    return $labels[$value] ?? $value;
}

function status_label(?string $status): string
{
    return enum_label((string) $status, [
        'OPEN' => 'Abierto',
        'CONVERTED' => 'Convertido',
        'LOST' => 'Perdido',
        'PENDING' => 'Pendiente',
        'COMPLETED' => 'Completada',
        'CANCELLED' => 'Cancelada',
    ]);
}

function stage_color_class(?string $value): string
{
    $stage = strtolower((string) $value);

    if (str_contains($stage, 'lost') || str_contains($stage, 'perdido')) {
        return 'lost';
    }

    if (str_contains($stage, 'convert') || str_contains($stage, 'socio')) {
        return 'converted';
    }

    if (str_contains($stage, 'trial') || str_contains($stage, 'prueba') || str_contains($stage, 'visit')) {
        return 'trial';
    }

    if (str_contains($stage, 'proposal') || str_contains($stage, 'propuesta') || str_contains($stage, 'alta')) {
        return 'proposal';
    }

    if (str_contains($stage, 'contact')) {
        return 'contacted';
    }

    if (str_contains($stage, 'new') || str_contains($stage, 'nuevo')) {
        return 'new';
    }

    return 'default';
}

function source_label(?string $source): string
{
    return enum_label((string) $source, [
        'WALK_IN' => 'Visita',
        'WEBSITE' => 'Web',
        'PHONE' => 'Telefono',
        'SOCIAL_MEDIA' => 'Redes',
        'REFERRAL' => 'Recomendacion',
        'OTHER' => 'Otro',
    ]);
}

function task_type_label(?string $type): string
{
    return enum_label((string) $type, [
        'SALES' => 'Comercial',
        'RETENTION' => 'Retencion',
        'PAYMENT' => 'Pago',
        'OPERATIONAL' => 'Operativa',
        'OTHER' => 'Otra',
    ]);
}
