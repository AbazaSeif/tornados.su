<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;


use Yii;
use yii\base\Model;

class Login extends Model {
    public $name;
    public $password;
    public $remember;

    public function rules() {
        return [
            [['name', 'password'], 'required'],
            ['name', 'string', 'min' => 4, 'max' => 24],
            ['password', 'string', 'min' => 1],
            ['remember', 'boolean'],
            ['remember', 'default', 'value' => true],
        ];
    }

    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'remember' => Yii::t('app', 'Remember Me'),
        ];
    }

    public static function login(User $user) {
        Yii::$app->user->login($user);
    }

    /**
     * @return User
     */
    public function getUser() {
        return User::findOne(['name' => $this->name]);
    }
}
