<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $model \app\models\UserMessage */
/* @var $ticket \app\models\Ticket */
/* @var $messages \app\models\UserMessage[] */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Переписка с пользователем создавшим заявку №<?= $ticket->id; ?></h4>
</div>

<div class="modal-body box-comments" style="max-height: 500px; overflow-y: scroll;">
    <?php if(!empty($messages)): ?>
        <?php foreach($messages as $comment): ?>
            <div class="box-comment">
                <img class="img-circle img-sm" src="<?= $comment->author->getAvatar(); ?>" alt="user image">
                <div class="comment-text">
                    <span class="username"><?= $comment->author->getFullName(); ?><span class="text-muted pull-right"><?= substr($comment->created_at,0,16); ?></span></span>
                    <?= $comment->message; ?>
                </div><!-- /.comment-text -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="box-comment">
            <p>У заявки пока что нет сообщений</p>
        </div>
    <?php endif; ?>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'create-message-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<div class="modal-body">
    <?= $form->field($model, 'message')->textarea()->label('Текст комментария'); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    <button type="button" data-ajax-form="#create-message-form" data-ok-reload=".modal-content" class="btn btn-primary">Добавить сообщение</button>
</div>

<?php ActiveForm::end(); ?>
