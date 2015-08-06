<?php
/**
 * @link http://zenothing.com/
 */

use app\modules\pyramid\models\Node;
use app\widgets\Ext;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View
 * @var $parent Node
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$user = isset($_GET['user']) ? $_GET['user'] : null;
$mine = !Yii::$app->user->isGuest && (Yii::$app->user->identity->isManager() || Yii::$app->user->identity->name == $user);
$manager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
$title = $this->title = Yii::t('app', 'Investments');
$columns = [
    'id',
    [
        'attribute' => 'user_name',
        'format' => 'html',
        'value' => function(Node $model) {
            return Html::a($model->user_name, ['/user/view', 'name' => $model->user_name]);
        }
    ],
    'time:datetime',
    [
        'label' => Yii::t('app', 'Action'),
        'format' => 'html',
        'value' => function(Node $model) {
            return Html::a(Yii::t('app', 'View'), ['index', 'id' => $model->id],
                ['class' => 'btn btn-primary btn-xs']);
        }
    ]
];


if (isset($parent)) {
    $title = $parent->getType()->name . " #$parent->id ";
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Investments'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $title;
    $this->title = $title;
    $title .= $mine ? Html::a($parent->user_name, ['/user/view', 'name' => $parent->user_name]) : $parent->user_name;

}
else {
    $additional = [$columns[0],
        [
            'attribute' => 'type_id',
            'label' => Yii::t('app', 'Plan'),
            'format' => 'html',
            'value' => function(Node $model) {
                return Html::a($model->getType()->name, ['type/view', 'id' => $model->type_id]);
            }
        ]
    ];
    if ($user) {
        $this->title = Yii::t('app', 'Investments of user') . ' ' . $user;
        if ($mine) {
            $title = Yii::t('app', 'Investments of user') . ' ' . Html::a($user, ['user/view', 'name' => $user]);
        }
        else {
            $title = $this->title;
        }
    }
    $columns = array_merge($additional, array_slice($columns, 1));
}

?>
<div class="invest middle">
    <?= Ext::stamp() ?>
    <div>
        <h1><?= $title ?></h1>

        <div class="form-group">
            <?php
            if (empty($parent)) {
                if ($manager) {
                    echo Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-primary']);
                }
                else {
                    echo Html::a(Yii::t('app', 'Open'), ['/pyramid/type/index'], ['class' => 'btn btn-success']);
                }
            }
            elseif ($manager) {
                    echo implode(' ', [
                        Html::a(Yii::t('app', 'Update'), ['update', 'id' => $parent->id], ['class' => 'btn btn-primary']),
                        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $parent->id], ['class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ]]),
                        Html::a(Yii::t('app', 'Compute'), ['compute', 'id' => $parent->id], ['class' => 'btn btn-warning',
                            'data' => ['method' => 'post']])
                    ]);
                }
            ?>
        </div>

        <?php
        if (isset($parent)) {
            echo Html::tag('p', Yii::t('app', 'Remains to exit') . ': ' . $parent->countQueue());
        }
        ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '',
        'showOnEmpty' => false,
        'columns' => $columns,
    ])
    ?>

</div>
