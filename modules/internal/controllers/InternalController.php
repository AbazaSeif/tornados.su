<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\internal\controllers;
use yii\web\Controller;
use yii\web\Response;
use Yii;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class InternalController extends Controller {
    public function actionSession() {
        foreach($_GET as $key => $value) {
            Yii::$app->session->set($key, $value);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Yii::$app->session;
    }
}
