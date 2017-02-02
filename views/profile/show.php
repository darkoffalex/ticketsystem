<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $ticket app\models\Ticket */
/* @var $user \app\models\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Constants;

$this->title = 'Личный кабинет';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact" style="max-width: 500px; margin: 0 auto;">
    <div class="row">

        <div class="col-lg-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Заявка № <?= $ticket->id; ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">

                    <ul class="todo-list ui-sortable">
                        <li class="">

                            <small class="text"><?= $ticket->created_at; ?></small>

                            <?php if($ticket->status_id == Constants::STATUS_NEW): ?>
                                <small class="label label-danger">Открыта</small>
                            <?php elseif($ticket->status_id == Constants::STATUS_IN_PROGRESS): ?>
                                <small class="label label-warning">Взята в обработку</small>
                            <?php elseif($ticket->status_id == Constants::STATUS_DONE): ?>
                                <small class="label label-success">Закрыта</small>
                            <?php endif; ?>

                            <?php if($ticket->hasOpenedQuestion()): ?>
                                <small class="label label-danger">Есть вопрос!</small>
                            <?php endif; ?>

                            <hr style="margin-top: 10px;margin-bottom: 10px;">

                            <span class="text"><?= $ticket->text; ?></span>

                        </li>
                    </ul>

                </div><!-- /.box-body -->
                <div class="box-footer clearfix no-border">
                    <?php if($ticket->status_id == Constants::STATUS_NEW): ?>
                        <a data-toggle="modal" data-target=".modal" href="<?= Url::to(['/profile/append-ticket','id' => $ticket->id]); ?>" class="btn btn-default pull-right"><i class="fa fa-pencil"></i> Дополнить</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <div class="col-lg-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Сообщения</h3>
                </div>
                <div class="box-body box-comments">
                    <?php if(!empty($ticket->userMessages)): ?>
                        <?php foreach($ticket->userMessages as $comment): ?>
                            <div class="box-comment">
                                <img class="img-circle img-sm" src="<?= $comment->author->getAvatar(); ?>" alt="user image">
                                <div class="comment-text">
                                    <span class="username">
                                        <?php $name = $comment->author->role_id == Constants::ROLE_NEW ? $comment->author->getFullName() : 'Поддержка'; ?>
                                        <?= $name; ?><span class="text-muted pull-right"><?= substr($comment->created_at,0,16); ?></span></span>
                                    <?= $comment->message; ?>
                                </div><!-- /.comment-text -->
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="box-comment">
                            <p>Нет сообщений и вопросов</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer clearfix no-border">
                    <?php if($ticket->status_id != Constants::STATUS_DONE): ?>
                        <a data-toggle="modal" data-target=".modal" href="<?= Url::to(['/profile/comment-ticket','id' => $ticket->id]); ?>" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Добавить сообщение</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
