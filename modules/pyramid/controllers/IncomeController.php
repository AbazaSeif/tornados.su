<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\controllers;
use app\modules\pyramid\models\Income;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\modules\pyramid\models\search\Income as IncomeSearch;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class IncomeController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ]
            ],

            'cache' => [
                'class' => 'yii\filters\HttpCache',
                'cacheControlHeader' => 'must-revalidate, private',
                'only' => ['index'],
                'lastModified' => function ($action, $params) {
                    $query = Income::find();
                    if (isset($params['user'])) {
                        $query->where(['user_name' => $params['user']]);
                    }
                    return (int) $query->max('time');
                },
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new IncomeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
}
