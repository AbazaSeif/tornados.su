<?php
/**
 * @link http://zenothing.com/
*/
use app\models\User;
use tests\Form;
use yii\console\Application;

/**
 * @var $users User[]
 * @var $config array
 */

require_once __DIR__ . '/../config/boot.php';

$app = new Application($config);

require_once 'Form.php';

function open_matrix($username)
{
    $form = new Form('/login', 'Login');
    $form->fill([
        'name' => $username,
        'password' => '1',
        'remember' => '0'
    ]);
    $form->send();

    $raw = $form->go('/plan4/open');
    file_put_contents(__DIR__ . "/../web/out/$username.html", $raw);
    $form->go('/user/logout');
}

$start = ((int) isset($argv[1]) ? $argv[1] : 2);
$users = User::find()->select('name')->where('id >= ' . $start)->orderBy(['id' => SORT_ASC])->all();

foreach($users as $user) {
    echo "# User: $user->name\n";
    open_matrix($user->name);
}
