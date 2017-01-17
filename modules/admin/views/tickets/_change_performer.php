<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $model \app\models\Ticket */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Делегировать тикет №<?= $model->id; ?></h4>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'change-performer-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'performer_id')->widget(Select2::classname(),[
        'initValueText' => !empty($model->performer) ? $model->performer->name.' '.$model->performer->surname : '',
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

    ]); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    <button type="button" data-ajax-form="#change-performer-form" data-ok-reload="#ticket-item-<?= $model->id; ?>" class="btn btn-primary">Сохранить</button>
</div>

<?php ActiveForm::end(); ?>

