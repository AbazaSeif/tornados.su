<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\models;

use Yii;
use yii\base\Model;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 *
 * @property integer $id
 * @property string $name
 * @property number $stake
 * @property number $income
 * @property number $profit
 * @property number $bonus
 * @property integer $degree
 */
class Type extends Model
{
    private static $_all;
    public $id;
    public $name;
    public $stake;
    public $income;
    public $profit;
    public $bonus;
    public $degree;
    public $visibility;
    public $reinvest;
    public $next_id;
    const LEVEL1 = 4;
    const LEVEL2 = 5;
    const LEVEL3 = 6;

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'stake' => Yii::t('app', 'Stake'),
            'income' => Yii::t('app', 'Income'),
            'bonus' => Yii::t('app', 'Bonus'),
        ];
    }

    /**
     * @return Type[]
     */
    public static function all() {
        if (!static::$_all) {
            Type::create(1, 'Calm', 10, 17, 0, 1);
            Type::create(2, 'Breeze', 30, 50, 2, 2);
            Type::create(3, 'Vortex', 60, 100, 3, 3);
            Type::create(Type::LEVEL1, 'Tornado', 100, 0, 5, null, true, 3, Type::LEVEL2);
            Type::create(Type::LEVEL2, 'Tornado', 0, 250, 0, null, false, 2, Type::LEVEL3);
            Type::create(Type::LEVEL3, 'Tornado', 0, 200, 0, 4, false, 1);
        }
        return static::$_all;
    }

    /**
     * @param $id
     * @return Type|null
     */
    public static function get($id) {
        $types = Type::all();
        return isset($types[$id]) ? $types[$id] : null;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return array
     */
    public static function getItems() {
        $items = [];
        foreach(static::all() as $type) {
            if ($type->visibility) {
                $items[$type->id] = Yii::t('app', $type->name);
            }
        }
        return $items;
    }

    /**
     * @return integer
     */
    public function getProfit() {
        return $this->income ? $this->stake * 2 - $this->income : 0;
    }

    public function getReinvest() {
        return $this->stake;
    }

    public static function create($id, $name, $stake, $income, $bonus,
                                  $reinvest, $visibility = true, $degree = 3, $next_id = null) {
        return static::$_all[$id] = new Type([
            'id' => $id,
            'name' => Yii::t('app', $name),
            'stake' => $stake,
            'income' => $income,
            'bonus' => $bonus,
            'reinvest' => $reinvest,
            'visibility' => $visibility,
            'degree' => $degree,
            'next_id' => $next_id
        ]);
    }

    public function isTornado() {
        return in_array($this->id, [Type::LEVEL2, Type::LEVEL3]);
    }
}
