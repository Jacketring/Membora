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

function country_dial_codes(): array
{
    return [
        'Espana' => '+34',
        'Portugal' => '+351',
        'Francia' => '+33',
        'Italia' => '+39',
        'Alemania' => '+49',
        'Reino Unido' => '+44',
        'Irlanda' => '+353',
        'Paises Bajos' => '+31',
        'Belgica' => '+32',
        'Suiza' => '+41',
        'Austria' => '+43',
        'Dinamarca' => '+45',
        'Suecia' => '+46',
        'Noruega' => '+47',
        'Finlandia' => '+358',
        'Polonia' => '+48',
        'Rumania' => '+40',
        'Marruecos' => '+212',
        'Estados Unidos' => '+1',
        'Canada' => '+1',
        'Mexico' => '+52',
        'Argentina' => '+54',
        'Chile' => '+56',
        'Colombia' => '+57',
        'Peru' => '+51',
        'Ecuador' => '+593',
        'Venezuela' => '+58',
        'Uruguay' => '+598',
        'Paraguay' => '+595',
        'Brasil' => '+55',
        'China' => '+86',
        'Japon' => '+81',
        'Corea del Sur' => '+82',
        'India' => '+91',
        'Australia' => '+61',
    ];
}

function country_dial_options(): array
{
    $options = [];
    foreach (country_dial_codes() as $country => $code) {
        $options[] = $country . ' ' . $code;
    }

    return $options;
}

function phone_country_value(?string $phone): string
{
    $phone = trim((string) $phone);
    if ($phone === '') {
        return 'Espana +34';
    }

    $codes = country_dial_codes();
    uasort($codes, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

    foreach ($codes as $country => $code) {
        if (str_starts_with($phone, $code)) {
            return $country . ' ' . $code;
        }
    }

    if (preg_match('/^(\+\d{1,4})/', $phone, $matches)) {
        return $matches[1];
    }

    return 'Espana +34';
}

function phone_local_value(?string $phone): string
{
    $phone = trim((string) $phone);
    if ($phone === '') {
        return '';
    }

    $countryValue = phone_country_value($phone);
    if (preg_match('/(\+\d{1,4})/', $countryValue, $matches) && str_starts_with($phone, $matches[1])) {
        return trim(substr($phone, strlen($matches[1])));
    }

    return $phone;
}

function phone_from_post(): ?string
{
    $country = post_value('phone_country', '');
    $number = post_value('phone_number', '');

    if ($number === '') {
        return null;
    }

    preg_match('/(\+\d{1,4})/', (string) $country, $matches);
    $prefix = $matches[1] ?? '';
    $cleanNumber = preg_replace('/[^\d\s().-]/', '', $number) ?? $number;

    return trim($prefix . ' ' . trim($cleanNumber));
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
