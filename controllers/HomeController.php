<?php
/**
 * @link http://zenothing.com/
 */

namespace app\controllers;


use app\models\Record;
use app\modules\article\models\Article;
use app\modules\invoice\models\Invoice;
use app\models\User;
use app\helpers\SQL;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class HomeController extends Controller {
    public function actionIndex() {
        return $this->render('index', [
            'statistics' => $this->renderPartial('statistics', ['statistics' => static::statistics()]),
            'news' => Article::find()->where('"name" is null')->orderBy(['id' => SORT_DESC])->limit(3)->all()
        ]);
    }

    public function actionError() {
        $exception = Yii::$app->getErrorHandler()->exception;
        $message = $exception->getMessage();
        if ($exception instanceof ForbiddenHttpException) {
            $message = $message ? Yii::t('app', $message) : Yii::t('app', 'Forbidden');
        }
        if ($message) {
            Yii::$app->session->setFlash('error', $message);
        }
        return $this->render('error', [
            'exception' => $exception
        ]);
    }

    public static function statistics() {
        $started = strtotime(SQL::queryCell('SELECT "time" FROM "journal" WHERE id = 1'));
        $invested = (int) SQL::queryCell('SELECT count(*) FROM "node"');
        return [
            'Started' => date('d-m-Y', $started),
            'Running days' => floor((time() - $started)/(3600 * 24)),
            'Users' => User::find()->count(),
            'Total deposited' => Invoice::find()->where(['or',
                ['status' => 'success'],
                ['status' => 'delete']
            ])->andWhere('amount > 0')->sum('amount'),
            'Total withdraw' => - Invoice::find()->where(['or',
                ['status' => 'success'],
                ['status' => 'delete']
            ])->andWhere('amount < 0')->sum('amount'),
            'Number of investments' => $invested
        ];
    }
}
