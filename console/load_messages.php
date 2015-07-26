<?php
/**
 * @link http://zenothing.com/
*/
use yii\console\Application;

require_once __DIR__ . '/../boot.php';

$messages = require $argv[1];

$app = new Application($config);

foreach($messages as $message => $translation) {
    $app->db->createCommand('CALL translate(:message, :translation)', [
        ':message' => $message,
        ':translation' => $translation
    ])->execute();

//    echo "CALL translate('$message', '$translation');\n";
}
