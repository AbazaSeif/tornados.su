<?php
/**
 * @link http://zenothing.com/
*/

namespace app\widgets;


use DateTime;
use yii\helpers\Html;

class Ext {
    public static function stamp() {
        return Html::tag('div', implode('', [
            Html::a('Developed by zenothing.com', 'http://zenothing.com/'),
            "\n\n :: zenothing.com :: \n\n ---------------------- user agent info ---------------------- \n\n  ",
            implode("\n  ", [
                implode(' ', [$_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_HOST'], $_SERVER['REMOTE_ADDR']]),
                date(DateTime::RFC822, $_SERVER['REQUEST_TIME']),
                $_SERVER['HTTP_USER_AGENT']
            ]),
            "\n\n ---------------------- end user agent info ------------------ \n\n"
        ]), [
            'style' => 'display: none'
        ]);
    }
}
