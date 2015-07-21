<?php
/**
 * @link http://zenothing.com/
 */

namespace app\helpers;


use Yii;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class SQL {
    public static function query($sql, $params = null) {
        $command = Yii::$app->db->createCommand($sql, $params);
        $command->execute();
        return $command;
    }

    public static function queryObject($sql, $params = null) {
        return static::query($sql, $params)->pdoStatement->fetchObject();
    }

    public static function queryColumn($sql, $params = null) {
        return static::query($sql, $params)->queryColumn();
    }

    public static function queryCell($sql, $params = null) {
        return static::query($sql, $params)->pdoStatement->fetchColumn();
    }
}
