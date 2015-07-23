<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\controllers;
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
            ]
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
