<?php
/**
 * @link http://zenothing.com/
*/
use yii\console\Application;

require_once __DIR__ . '/../config/boot.php';

$app = new Application($config);

$length = (int) $argv[1];
$transaction = Yii::$app->db->beginTransaction();
for($i = 2; $i < $length; $i++) {
    $name = 'user' . $i;
    $app->db->createCommand('INSERT INTO "user"("name", email) VALUES (:name, :email)', [
        ':name' => $name,
        ':email' => $name . '@yopmail.com'
    ])->execute();
    $names[] = $name;
}

$app->db->createCommand('UPDATE "user" SET "account" = 1000,
"hash" = \'$2y$10$zEiSdGHD2q9fRtljNONCbuj15hjLMNTU71IaM5PcR503kNz3VfC7W\'')->execute();
$transaction->commit();
