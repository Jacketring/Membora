<?php

declare(strict_types=1);

function membora_public_origin(): string
{
    $configured = rtrim(trim((string) getenv('MEMBORA_PUBLIC_ORIGIN')), '/');
    if ($configured !== '' && filter_var($configured, FILTER_VALIDATE_URL)) {
        return $configured;
    }

    $host = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? 'membora.es')));
    $isMemboraHost = preg_match('/^(?:www\.)?membora\.es(?::\d+)?$/', $host) === 1;
    $isLocalHost = preg_match('/^(?:localhost|127\.0\.0\.1)(?::\d+)?$/', $host) === 1;
    if (!$isMemboraHost && !$isLocalHost) {
        $host = 'membora.es';
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    return ($https || !$isLocalHost ? 'https://' : 'http://') . $host;
}

