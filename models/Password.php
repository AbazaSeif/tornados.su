<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;


use Yii;
use yii\base\Model;

/**
 * Class Password
 * @property User user
 * @package app\models
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Password extends Model {
    public $user_name;
    private $_user;
    public $password;
    public $new_password;
    public $repeat_password;

    public function rules() {
        return [
            [['password', 'new_password', 'repeat_password'], 'required'],
            ['repeat_password', 'compare', 'compareAttribute' => 'new_password']
        ];
    }

    public function scenarios() {
        return [
            'default' => ['password', 'new_password', 'repeat_password'],
            'reset' => ['new_password', 'repeat_password'],
        ];
    }

    public function attributeLabels() {
        return [
            'password' => Yii::t('app', 'Current password'),
            'new_password' => Yii::t('app', 'New password'),
            'repeat_password' => Yii::t('app', 'Repeat password'),
        ];
    }

    public function setUser(User $value) {
        $this->user_name = $value->name;
        $this->_user = $value;
    }

    public function getUser() {
        if (!$this->_user) {
            $this->_user = User::findOne(['name' => $this->user_name]);
        }
        return $this->_user;
    }
}
