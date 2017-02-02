<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $ticket \app\models\Ticket */
/* @var $model \app\models\UserMessage */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Добавление сообщения к тикету №<?= $ticket->id; ?></h4>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'create-message-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'message')->textarea()->label('Текст сообщения'); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    <button type="submit" class="btn btn-primary">Добавить</button>
</div>

<?php ActiveForm::end(); ?>

