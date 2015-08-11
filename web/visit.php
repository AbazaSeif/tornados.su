<?php
/**
 * @link http://zenothing.com/
 */

define('CONFIG', __DIR__ . '/../config');
define('YII_DEBUG', false);
define('YII_ENV_DEV', 'prod');

$ip = $_SERVER['REMOTE_ADDR'];
$path = $_SERVER['HTTP_REFERER'];
if (0 === strpos($path, 'http://tornados.su/')) {
    $path = str_replace('http://tornados.su/', '', $path);
}
if ('GET' != $_SERVER['REQUEST_METHOD']
    || !isset($_GET['spend'])
    || !$path || in_array($ip, ['127.0.0.1', '::1'])) {
    http_response_code(400);
    exit;
}
$spend = (int) $_GET['spend'];
$width = (int) $_GET['width'];
$height = (int) $_GET['height'];
$heap = isset($_GET['heap']) ? (int) $_GET['heap'] : null;

require CONFIG . '/common.php';
require CONFIG . '/web.php';
require CONFIG . '/local.php';

$db = $config['components']['db'];
$pdo = new PDO($db['dsn'], $db['username'], $db['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$st = $pdo->prepare('SELECT id FROM "visit_agent" WHERE "agent" = :agent AND "ip" = :ip');
$st->execute([
    ':agent' => $_SERVER['HTTP_USER_AGENT'],
    ':ip' => $ip
]);
$agent_id = $st->fetchColumn();

if (!$agent_id) {
    $st = $pdo->prepare('INSERT INTO "visit_agent"("agent", "ip", "width", "height")
                         VALUES (:agent, :ip, :width, :height) RETURNING id');
    $st->execute([
        ':agent' => $_SERVER['HTTP_USER_AGENT'],
        ':ip' => $ip,
        ':width' => $width,
        ':height' => $height
    ]);
    $agent_id = $st->fetchColumn();
}

$st = $pdo->prepare('INSERT INTO "visit_path"("agent_id", "path", "spend", "heap")
                     VALUES (:agent_id, :path, :spend, :heap)');
$st->execute([
    ':agent_id' => $agent_id,
    ':path' => $path,
    ':spend' => $spend,
    ':heap' => $heap
]);

header('Content-Type: application/javascript');
