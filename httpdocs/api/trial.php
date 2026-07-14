<?php

declare(strict_types=1);

require __DIR__ . '/_origin.php';

$webOrigin = membora_public_origin();
$remoteUrl = $webOrigin . '/app/api/trial';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($payload)) {
    $payload = $_POST;
}

if (!empty($payload['website'])) {
    echo json_encode(['success' => true, 'message' => 'Revisa tu correo para continuar.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$body = json_encode($payload, JSON_UNESCAPED_UNICODE);
if ($body === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (function_exists('curl_init')) {
    $ch = curl_init($remoteUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
            'Origin: ' . $webOrigin,
            'Referer: ' . $webOrigin . '/',
        ],
    ]);
    $responseBody = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
} else {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Accept: application/json\r\nContent-Type: application/json\r\nOrigin: {$webOrigin}\r\nReferer: {$webOrigin}/\r\n",
            'content' => $body,
            'ignore_errors' => true,
            'timeout' => 20,
        ],
    ]);
    $responseBody = @file_get_contents($remoteUrl, false, $context);
    $status = 0;
    foreach (($http_response_header ?? []) as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $matches)) {
            $status = (int) $matches[1];
            break;
        }
    }
}

$responsePayload = json_decode(is_string($responseBody) ? $responseBody : '', true);
if (!is_array($responsePayload)) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar con el alta de prueba.'], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code($status >= 200 && $status < 600 ? $status : 502);
echo json_encode($responsePayload, JSON_UNESCAPED_UNICODE);
