<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;


use Yii;
use yii\base\Model;

class ResetRequest extends Model {
    public $email;


    public function rules() {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'exist',
                'targetClass' => 'app\models\User',
                'message' => Yii::t('app', 'User with this e-mail does not exist')],
        ];
    }
}
