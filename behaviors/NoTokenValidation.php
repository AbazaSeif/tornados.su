<?php
/**
 * @link http://zenothing.com/
 */
namespace app\behaviors;


use Yii;
use yii\base\ActionFilter;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class NoTokenValidation extends ActionFilter {
    public function beforeAction($action) {
        if (in_array(Yii::$app->controller->action->id, $this->only)) {
            Yii::$app->controller->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}
