<?php
/**
 * @link http://zenothing.com/
*/

namespace app\helpers;


class Account {
    /**
     * @param $name
     * @return float
     */
    public static function get($name) {
        return (float) SQL::queryCell('SELECT "' . $name . '" FROM "account"');
    }

    /**
     * @param string $name
     * @param float $value
     */
    public static function set($name, $value) {
        if ($value) {
            SQL::query('UPDATE "account" SET "' . $name . '" = :value', [
                ':value' => (float) $value
            ]);
        }
    }

    /**
     * @param string $name
     * @param float $value
     */
    public static function add($name, $value) {
        if ($value) {
            SQL::query('UPDATE "account" SET "' . $name . '" = "' . $name . '" + :value', [
                ':value' => (float) $value
            ]);
        }
    }
}
