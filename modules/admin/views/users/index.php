<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\helpers\Constants;
use app\helpers\Help;

/* @var $searchModel \app\models\UserSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\UsersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = 'Список пользователей';
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    ['attribute' => 'username'],
    ['attribute' => 'name'],
    ['attribute' => 'surname'],

    [
        'attribute' => 'role_id',
        'filter' => [
            Constants::ROLE_ADMIN => 'Администратор',
            Constants::ROLE_REDACTOR => 'Редактор',
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            $roles = [
                Constants::ROLE_ADMIN => 'Администратор',
                Constants::ROLE_REDACTOR => 'Редактор',
            ];

            return !empty($roles[$model->role_id]) ? $roles[$model->role_id] : 'Неизвестен';
        },
    ],

//    [
//        'attribute' => 'last_online_at',
//        'filter' => \kartik\daterange\DateRangePicker::widget([
//            'model' => $searchModel,
//            'convertFormat' => true,
//            'attribute' => 'last_online_at',
//            'pluginOptions' => [
//                'locale' => [
//                    'format'=>'Y-m-d',
//                    'separator'=>' - ',
//                ],
//            ],
//        ]),
//        'enableSorting' => true,
//        'format' => 'raw',
//        'value' => function ($model, $key, $index, $column){
//            /* @var $model \app\models\User */
//            return !empty($model->last_online_at) ? $model->last_online_at : Yii::t('admin','No data');
//        },
//    ],


    [
        'attribute' => 'created_at',
        'filter' => \kartik\daterange\DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'attribute' => 'created_at',
            'pluginOptions' => [
                'locale' => [
                    'format'=>'Y-m-d',
                    'separator'=>' - ',
                ],
            ],
        ]),
        'enableSorting' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            return !empty($model->created_at) ? $model->created_at : 'Нет данных';
        },
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::STATUS_ENABLED => 'Активирован',
            Constants::STATUS_DISABLED => 'Деактивирован',
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            $statuses = [
                Constants::STATUS_ENABLED => '<span class="label label-success">Активировн</span>',
                Constants::STATUS_DISABLED => '<span class="label label-danger">Деактивирован</span>',
            ];

            return !empty($statuses[$model->status_id]) ? $statuses[$model->status_id] : 'Нет данных';
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 100px; text-align: center;'],
        'header' => 'Действия',
        'template' => '{delete} &nbsp; {update} &nbsp; {change_status} &nbsp; {preview}',
        'buttons' => [
            'change_status' => function ($url,$model,$key) {
                /* @var $model \app\models\User */
                $icon = $model->status_id == Constants::STATUS_ENABLED ? 'glyphicon glyphicon-check' : 'glyphicon glyphicon-unchecked';
                $message = $model->status_id == Constants::STATUS_ENABLED ? 'Активировать' : 'Деактивировать';
                return Html::a('<span class="'.$icon.'"></span>', Url::to(['/admin/users/status', 'id' => $model->id]), ['title' => $message]);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) {return $model->id != Yii::$app->user->id;},
            'update' => true,
            'change_status' => function ($model, $key, $index) {return $model->id != Yii::$app->user->id;},
        ],
    ],
];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Список</h3>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'pjax' => false,
                ]); ?>
            </div>
            <div class="box-footer">
                <a href="<?php echo Url::to(['/admin/users/create']); ?>" class="btn btn-primary">Создать</a>
            </div>
        </div>
    </div>
</div>
