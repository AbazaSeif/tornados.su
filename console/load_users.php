<?php
use yii\console\Application;

require_once __DIR__ . '/../boot.php';

$file = fopen($argv[1], 'r');

$app = new Application($config);

//$transaction = $app->db->beginTransaction();
while($line = fgetcsv($file, 0, ';')) {
    $row = [];
    foreach($line as $cell) {
        $cell = trim($cell);
        if (empty($cell)) {
            $cell = null;
        }
        $row[] = $cell;
    }
    $password = $app->security->generateRandomString(12);
    try {
        $command = $app->db->createCommand('INSERT INTO "user"("name", email, skype, perfect, "hash", "status")
            VALUES (:name, :email, :skype, :perfect, :hash, 2)', [
            ':name' => $row[0],
            ':email' => $row[1],
            ':skype' => $row[2],
            ':perfect' => $row[3],
            ':hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $command->execute();
        echo $row[0] . ";$password\n";
    }
    catch (Exception $ex) {
        echo $ex->getMessage();
        echo implode("\t", $row);
    }
}
//$transaction->commit();
