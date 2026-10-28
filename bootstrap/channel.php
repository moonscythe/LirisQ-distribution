<?php
declare(strict_types=1);
// Development remains private. Beta is a signed-in streamer release channel
// published from the approved distribution repository.
$_GET['action'] = $_GET['action'] ?? 'manifest';
$channel = (string)($_GET['channel'] ?? 'development');
if (!in_array($channel, ['development', 'beta'], true)) { http_response_code(404); exit; }
if ($channel === 'beta') {
    $runtimeRoot = null;
    foreach ([dirname(__DIR__), dirname(__DIR__, 2)] as $candidate) if (is_file($candidate . '/runtime-guard.php')) { $runtimeRoot = $candidate; break; }
    if ($runtimeRoot === null) { http_response_code(500); exit('LirisQ runtime guard is unavailable.'); }
    require_once $runtimeRoot . '/runtime-guard.php';
    lirisq_require_secure_php();
    if (session_status() !== PHP_SESSION_ACTIVE) session_start(['cookie_httponly'=>true, 'cookie_secure'=>true, 'cookie_samesite'=>'Lax']);
    if (($_SESSION['lirisq_role'] ?? '') !== 'streamer') { http_response_code(403); exit; }
    if (($_GET['action'] ?? 'manifest') !== 'manifest' || !function_exists('curl_init')) { http_response_code(404); exit; }
    $curl = curl_init('https://api.github.com/repos/moonscythe/LirisQ-distribution/contents/latest/manifest.json?ref=beta');
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_FAILONERROR=>true, CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS, CURLOPT_HTTPHEADER=>['User-Agent: LirisQ-Updater'], CURLOPT_CONNECTTIMEOUT=>10, CURLOPT_TIMEOUT=>30]);
    $body = curl_exec($curl); $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE); curl_close($curl);
    $payload = is_string($body) ? json_decode($body, true) : null;
    $manifest = is_array($payload) && is_string($payload['content'] ?? null) ? base64_decode(preg_replace('/\s+/', '', $payload['content']), true) : false;
    if ($status < 200 || $status >= 300 || !is_string($manifest) || strlen($manifest) > 1024 * 1024) { http_response_code(502); exit; }
    header('content-type: application/json; charset=utf-8'); header('cache-control: no-store'); exit($manifest);
}
$_GET['channel'] = $channel;
require __DIR__ . '/development.php';
