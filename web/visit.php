<?php
/**
 * @link http://zenothing.com/
 */

$ip = $_SERVER['REMOTE_ADDR'];
$path = $_SERVER['HTTP_REFERER'];
if (0 === strpos($path, 'http://diamond-rush.ru/')) {
    $path = str_replace('http://diamond-rush.ru/', '', $path);
}
if ('GET' != $_SERVER['REQUEST_METHOD']
    || !isset($_GET['spend'])
    || !$path || in_array($ip, ['127.0.0.1', '::1'])) {
    http_response_code(400);
    exit;
}
$spend = (int) $_GET['spend'];

define('CONFIG', __DIR__ . '/../config');

require CONFIG . '/common.php';
require CONFIG . '/web.php';

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
    $st = $pdo->prepare('INSERT INTO "visit_agent"("agent", "ip") VALUES (:agent, :ip) RETURNING id');
    $st->execute([
        ':agent' => $_SERVER['HTTP_USER_AGENT'],
        ':ip' => $ip
    ]);
    $agent_id = $st->fetchColumn();
}

$st = $pdo->prepare('INSERT INTO "visit_path"("agent_id", "path", "spend") VALUES (:agent_id, :path, :spend)');
$st->execute([
    ':agent_id' => $agent_id,
    ':path' => $path,
    ':spend' => $spend
]);

header('Content-Type: application/javascript');
