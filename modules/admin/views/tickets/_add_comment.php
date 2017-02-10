<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $ticket \app\models\Ticket */
/* @var $model \app\models\TicketComment */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Добавление комментария к заявке №<?= $ticket->id; ?></h4>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'create-comment-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'text')->textarea()->label('Текст комментария'); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    <button type="button" data-ajax-form="#create-comment-form" data-ok-reload="#ticket-item-<?= $ticket->id; ?>" class="btn btn-primary">Сохранить</button>
</div>

<?php ActiveForm::end(); ?>

