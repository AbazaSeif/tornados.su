<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\controllers;


use app\behaviors\Access;
use app\modules\pyramid\models\Gift;
use app\modules\pyramid\models\Node;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class NodeController extends Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class' => Access::class,
                'manager' => ['create', 'update', 'delete']
            ],

            'cache' => [
                'class' => 'yii\filters\HttpCache',
                'cacheControlHeader' => 'must-revalidate, private',
                'only' => ['index'],
                'enabled' => true,
                'lastModified' => function ($action, $params) {
                    $query = Node::find();
                    if (isset($params['user'])) {
                        $query->where(['user_name' => $params['user']]);
                    }
                    return (int) $query->max('time');
                },
            ],
        ];
    }

    public function actionIndex($user = null, $id = null) {
        $parent = $id ? $this->findModel($id) : null;
        $query = Node::find()
            ->orderBy(['time' => SORT_ASC, 'id' => SORT_ASC]);
        if ($user) {
            $query->andWhere(['user_name' => $user]);
        }
        elseif ($parent) {
            $query
                ->andWhere('"time" < :time', [':time' => $parent->time])
                ->andWhere(['type_id' => $parent->type_id]);
        }
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query
            ]),
            'parent' => $parent
        ]);
    }

    public function actionCreate($id = null) {
        $model = new Node();
        if ($id) {
            $base = $this->findModel($id);
            $model->time = $base->time - 1;
            $model->type_id = $base->type_id;
        }
        else {
            $model->time = time();
        }

        $is_post = $model->load(Yii::$app->request->post());
        if ($is_post) {
            $model->count = $model->getType()->degree;
            if ($is_post && $model->validate()) {
                if ($model->save(false)) {
                    return $this->redirect(['invest', 'id' => $model->id]);
                }
            }
            else {
                Yii::$app->session->setFlash('error', json_encode($model->errors, JSON_UNESCAPED_UNICODE));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['invest', 'id' => $model->id]);
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

    public function actionGift() {
        return $this->render('gift', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Gift::find(),
                'sort' => [
                    'defaultOrder' => ['id' => SORT_DESC]
                ]
            ])
        ]);
    }

    public function actionGive($id) {
        $node = Gift::findOne($id)->give();
        if ($node) {
            return $this->redirect(['index', 'id' => $node->id]);
        }
        else {
            Yii::$app->session->addFlash('error', Yii::t('app', 'Something wrong happened'));
            return $this->redirect(['gift']);
        }
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Node the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Node::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
