<?php

declare(strict_types=1);

require __DIR__ . '/Support.php';

$isSecureRequest = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isSecureRequest,
    'httponly' => true,
    'samesite' => 'Lax',
]);

send_security_headers($isSecureRequest);

session_start();

require __DIR__ . '/Database.php';
require __DIR__ . '/Auth.php';
require __DIR__ . '/Mailer.php';
require __DIR__ . '/Repositories.php';
require __DIR__ . '/Actions.php';
