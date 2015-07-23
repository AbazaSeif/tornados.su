<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\controllers;


use app\modules\pyramid\models\Node;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\pyramid\models\Type;
use yii\web\NotFoundHttpException;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class TypeController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'open' => ['post'],
                ]
            ]
        ];
    }

    public function actionIndex() {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Type::find(),
                'sort' => [
                    'defaultOrder' => ['id' => SORT_ASC]
                ]
            ]),
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionOpen($id) {
        $node = new Node([
            'type' => $this->findModel($id),
            'user' => Yii::$app->user->identity
        ]);

        if ($node->user->account >= $node->type->stake) {
            $node->user->account -= $node->type->stake;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($node->user->update(true, ['account']) && $node->invest()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'The plan is open'));
                }
            }
            catch(\Exception $ex) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Insufficient funds'));
            return $this->redirect(['view', 'id' => $node->id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Type::get($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
