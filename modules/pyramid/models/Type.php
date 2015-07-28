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
 * @property integer $degree
 */
class Type extends Model
{
    private static $_all = [];
    public $id;
    public $name;
    public $stake;
    public $income;
    public $profit;
    public $degree = 3;

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'stake' => Yii::t('app', 'Stake'),
            'income' => Yii::t('app', 'Income'),
        ];
    }

    /**
     * @return Type[]
     */
    public static function all() {
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
            $items[$type->id] = Yii::t('app', $type->name);
        }
        return $items;
    }

    /**
     * @return integer
     */
    public function getProfit() {
        return $this->stake * 2 - $this->income;
    }

    public function getReinvest() {
        return $this->stake;
    }

    public static function create($id, $name, $stake, $income) {
        static::$_all[] = new Type([
            'id' => $id,
            'name' => Yii::t('app', $name),
            'stake' => $stake,
            'income' => $income
        ]);
    }
}

Type::create(1, 'Calm', 10, 17);
Type::create(2, 'Breeze', 30, 50);
Type::create(3, 'Vortex', 60, 100);
Type::create(4, 'Tornado', 100, 250);
