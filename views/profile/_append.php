<?php
use yii\bootstrap\ActiveForm;

/* @var $model \app\models\Ticket*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Добавить к сообщению тикета №<?= $model->id; ?></h4>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'append-to-ticket-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'appended_text')->textarea(); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    <button type="submit" class="btn btn-primary">Добавить</button>
</div>

<?php ActiveForm::end(); ?>

