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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::class,
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $command = Yii::$app->db->createCommand('INSERT INTO source_message("message") VALUES (:message) RETURNING id', [
                ':message' => $model->message
            ]);
            $command->execute();
            $id = (int) $command->pdoStatement->fetchColumn();
            Yii::$app->db->createCommand('INSERT INTO message(id, translation) VALUES (:id, :translation)', [
                ':id' => $id,
                ':translation' => $model->translation
            ])->execute();
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)  {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()  ) {
            Yii::$app->db->createCommand('UPDATE source_message SET "message" = :message WHERE id = :id', [
                ':id' => $model->id,
                ':message' => $model->message
            ])->execute();
            Yii::$app->db->createCommand('UPDATE "message" SET translation = :translation WHERE id = :id', [
                ':id' => $model->id,
                ':translation' => $model->translation
            ])->execute();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        Yii::$app->db->createCommand('DELETE FROM source_message WHERE id = :id', [
            ':id' => $id
        ])->execute();
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
        setcookie('lang', $code, time() + 3600 * 24 * 30, '/');
        return $this->goBack();
    }
}
