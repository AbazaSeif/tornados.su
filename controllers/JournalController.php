<?php
/**
 * @link http://zenothing.com/
 */

namespace app\controllers;

use app\behaviors\Access;
use Yii;
use app\models\Record;
use app\models\search\Record as JournalSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * JournalController implements the CRUD actions for Journal model.
 */
class JournalController extends Controller
{
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::class,
                'plain' => ['index', 'view']
            ]
        ];
    }

    public function actionIndex() {
        $searchModel = new JournalSearch();

        $params = Yii::$app->request->queryParams;
        if (!Yii::$app->user->identity->isManager()) {
            if (empty($params['user'])) {
                return $this->redirect(['index', 'user' => Yii::$app->user->identity->name]);
            }
            elseif (!Yii::$app->user->identity->isManager() && $params['user'] != Yii::$app->user->identity->name) {
                throw new ForbiddenHttpException('Forbidden');
            }
        }
        $dataProvider = $searchModel->search($params);

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

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Journal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Record the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Record::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
