<?php
use yii\console\Application;

require_once __DIR__ . '/../boot.php';

$app = new Application($config);

$command = $app->db->createCommand('SELECT ' . $argv[2] . ' FROM "' . $argv[1] . '"');
$command->execute();

$columns = [];
foreach(explode(',', $argv[2]) as $column) {
    $columns[] = "\"$column\"";
}
echo 'insert into "' . $argv[1] . '" (' . implode(', ', $columns) . ") values\n";
$lines = [];
while($row = $command->pdoStatement->fetch(PDO::FETCH_NUM)) {
    foreach($row as $i => $cell) {
        if (empty($cell)) {
            $row[$i] = 'NULL';
        }
        elseif (is_string($cell)) {
            $row[$i] = "'$cell'";
        }
    }

    $lines[] = '(' . implode(',', $row) . ')';
}

echo implode(",\n", $lines) . ';';
