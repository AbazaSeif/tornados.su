<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\lang\controllers;

use app\behaviors\Access;
use app\modules\lang\models\search\Translation as TranslationSearch;
use app\modules\lang\models\Translation;
use Yii;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class LangController extends Controller
{
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::className(),
                'manager' => ['index', 'view', 'create', 'update', 'delete'],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new TranslationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate() {
        $model = new Translation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)  {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Translation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Translation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChoice($code = 'en') {
        $cookies = Yii::$app->response->cookies;
        if ('en' == $code) {
            $cookies->add(new Cookie([
                'name' => 'lang',
                'value' => 'en',
                'expire' => time() + 3600 * 24 * 30
            ]));
        }
        else {
            $cookies->remove('lang');
        }
        return $this->goBack();
    }
}
