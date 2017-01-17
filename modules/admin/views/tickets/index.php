<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\helpers\Constants;
use app\helpers\Help;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $searchModel \app\models\UserSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\UsersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;
$view = $this;

$this->title = 'Список тикетов';
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'text',
        'format' => 'raw',
        'contentOptions'=>['colspan' => '5'],
        'value' => function ($model, $key, $index, $column) use ($view){
            /* @var $model \app\models\Ticket */
            return $view->render('_ticket_item',compact('model'));
        },
    ],

    [
        'attribute' => 'author_name',
        'format' => 'raw',
        'contentOptions'=>['style' => 'display:none;'],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Ticket */
            return null;
        },
    ],

    [
        'attribute' => 'performer_id',
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'performer_id',
            'initValueText' => !empty($searchModel->author) ? $searchModel->author->name.' '.$searchModel->author->surname : '',
            'options' => ['placeholder' => 'Найти пользователя'],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 2,
                'language' => [
                    'noResults' => new JsExpression("function () { return 'Нет результатов';}"),
                    'searching' => new JsExpression("function () { return 'Поиск...'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return 'Впишите больше символов'}"),
                    'errorLoading' => new JsExpression("function () { return 'Ожидание...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/users/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
            ],
        ]),
        'format' => 'raw',
        'contentOptions'=>['style' => 'display:none;'],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Ticket */
            return null;
        },
    ],

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
        'contentOptions'=>['style' => 'display:none;'],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            return null;
        },
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::STATUS_NEW => 'Новый',
            Constants::STATUS_IN_PROGRESS => 'В работе',
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'contentOptions'=>['style' => 'display:none;'],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            $statuses = [
                Constants::STATUS_NEW => '<span class="label label-success">Новый</span>',
                Constants::STATUS_IN_PROGRESS => '<span class="label label-danger">В работе</span>',
            ];

            return null;
        },
    ],

//    [
//        'class' => 'yii\grid\ActionColumn',
//        'contentOptions'=>['style'=>'width: 100px; text-align: center;'],
//        'header' => 'Действия',
//        'template' => '{delete} &nbsp; {update}',
//    ],
];

?>

<style type="text/css">
    .table-responsive > .table > thead > tr > th, .table-responsive > .table > tbody > tr > th,
    .table-responsive > .table > tfoot > tr > th, .table-responsive > .table > thead > tr > td,
    .table-responsive > .table > tbody > tr > td, .table-responsive > .table > tfoot > tr > td
    {
        white-space: normal;
    }
</style>

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
