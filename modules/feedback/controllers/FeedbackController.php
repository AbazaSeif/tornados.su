<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\feedback\controllers;

use app\behaviors\Access;
use app\modules\feedback\models\Feedback;
use Yii;
use app\modules\feedback\models\search\Feedback as FeedbackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class FeedbackController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::className(),
                'manager' => ['index', 'view', 'update', 'delete']
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new FeedbackSearch();
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

    public function actionCreate($template = null) {
        $model = new Feedback(['scenario' => Yii::$app->user->isGuest ? 'guest' : 'default']);

        if ($model->load(Yii::$app->request->post())) {
            if ('default' == $model->scenario) {
                $model->username = Yii::$app->user->identity->name;
            }
            if ($model->save()) {
                Yii::t('app', 'Your feedback will be reviewed soon');
                return $this->redirect(['/home/index']);
            }
        }
        elseif (!Yii::$app->user->isGuest && $template) {
            switch($template) {
                case 'wallet':
                    $model->subject = Yii::t('app', 'Change my wallet');
                    $model->content = Yii::t('app', 'Change my wallet {wallet} to', [
                        'wallet' => Yii::$app->user->identity->perfect
                    ]);
                    break;
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Feedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
