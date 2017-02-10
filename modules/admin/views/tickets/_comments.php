<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $model \app\models\Ticket */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\TicketsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title">Просмотр комментариев заявки №<?= $model->id; ?></h4>
</div>

<div class="modal-body box-comments" style="max-height: 500px; overflow-y: scroll;">

    <?php if(!empty($model->ticketComments)): ?>
        <?php foreach($model->ticketComments as $comment): ?>
            <div class="box-comment">
                <img class="img-circle img-sm" src="<?= $comment->author->getAvatar(); ?>" alt="user image">
                <div class="comment-text">
                    <span class="username"><?= $comment->author->name.' '.$comment->author->surname; ?><span class="text-muted pull-right"><?= substr($comment->created_at,0,16); ?></span></span>
                    <?= $comment->text; ?>
                </div><!-- /.comment-text -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="box-comment">
            <p>У заявки пока что нет комментариев</p>
        </div>
    <?php endif; ?>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
</div>

