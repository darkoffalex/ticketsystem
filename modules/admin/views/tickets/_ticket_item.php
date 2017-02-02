<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\helpers\Constants;
use app\helpers\Help;
use yii\helpers\StringHelper;
use branchonline\lightbox\Lightbox;

/* @var $model \app\models\Ticket */
/* @var $user \app\models\User */
/* @var $this \yii\web\View */

$user = Yii::$app->user->identity;
?>

<ul class="timeline" style="margin: 0;">
    <li style="margin:0;">
        <?php $colors = [
            Constants::STATUS_NEW => '#DD4B39',
            Constants::STATUS_IN_PROGRESS => '#F39C12',
            Constants::STATUS_DONE => '#414141',
        ]; ?>
        <div class="timeline-item" style="margin: 0; border: 1px solid <?= $colors[$model->status_id]; ?>;">

            <span class="time"><i class="fa fa-clock-o"></i> <?= substr($model->created_at,0,16); ?></span>
            <h3 class="timeline-header">
                <strong>ID:<?= $model->id; ?></strong>
                &nbsp;|&nbsp;
                Автор: <?= $model->author_name; ?>
                &nbsp;|&nbsp;
                <?php if($model->status_id == Constants::STATUS_NEW): ?>
                    <span class="label label-danger">Новый</span>
                <?php elseif($model->status_id == Constants::STATUS_IN_PROGRESS): ?>
                    <?php if($user->role_id == Constants::ROLE_ADMIN || $model->performer->id == $user->id): ?>
                        <a title='Отметить как "Отработан"' data-ajax-reload="#ticket-item-<?= $model->id; ?>" href="<?= Url::to(['/admin/tickets/status','id' => $model->id, 'status' => Constants::STATUS_DONE]); ?>" class="label label-warning">В работе</a>
                    <?php else: ?>
                        <span class="label label-warning">В работе</span>
                    <?php endif ?>
                <?php elseif($model->status_id == Constants::STATUS_DONE): ?>
                    <?php if($user->role_id == Constants::ROLE_ADMIN || $model->performer->id == $user->id): ?>
                        <a data-ajax-reload="#ticket-item-<?= $model->id; ?>" href="<?= Url::to(['/admin/tickets/status', 'id' => $model->id, 'status' => Constants::STATUS_IN_PROGRESS]); ?>" class="label label-success">Отработан</a>
                    <?php else: ?>
                        <span class="label label-success">Отработан</span>
                    <?php endif ?>
                <?php endif; ?>
                &nbsp;|&nbsp;<?= $model->status_id == Constants::STATUS_IN_PROGRESS ? 'Отрабатывается' : 'Исполнитель'; ?> : <?= !empty($model->performer) ? $model->performer->name.' '.$model->performer->surname : '(нет)'; ?>
                <?php if(empty($model->performer)): ?>
                    <a href="<?= Url::to(['/admin/tickets/take', 'id' => $model->id]); ?>" class="btn btn-primary btn-xs">Взять в обработку</a>
                <?php endif; ?>
                <?php if($user->role_id == Constants::ROLE_ADMIN): ?>
                    &nbsp;|&nbsp;
                    <a data-target=".modal" data-toggle="modal" href="<?= Url::to(['/admin/tickets/change-performer', 'id' => $model->id]); ?>" class="btn btn-primary btn-xs">Назначить</a>
                    <a data-confirm="Удалить тикет навсегда ?" href="<?= Url::to(['/admin/tickets/delete', 'id' => $model->id]); ?>" class="btn btn-primary btn-xs">Удалить</a>
                <?php endif; ?>
                <a href="<?= Url::to(['/admin/tickets/messages', 'id' => $model->id]); ?>" data-target=".modal" data-toggle="modal" class="btn btn-primary btn-xs">Переписка</a>
            </h3>


            <div class="timeline-body">
                <p><strong>Текст сообщения:</strong> <?= StringHelper::truncateWords($model->text,10); ?> &nbsp; <a class="text-sm" data-toggle="modal" data-target=".modal" href="<?= Url::to(['/admin/tickets/full-text', 'id' => $model->id]); ?>">(читать полностью)</a></p>
                <?php if(!empty($model->link)): ?>
                    <p><strong>Ссылка пользователя: <?= Html::a($model->link,$model->link,['target' => '_blank']); ?></strong></p>
                <?php endif; ?>
                <hr>
                <div class="row">
                    <div class="col-md-7">
                        <h4>Скриншоты пользователя:</h4>
                        <?php $images = $model->getImageFilesData(); ?>
                        <?php if(!empty($images)): ?>
                            <?= Lightbox::widget([
                                'files' => $model->getImageFilesData()
                            ]); ?>
                        <?php else: ?>
                            <p>Нет скриншотов</p>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-5">
                        <div style="height: 100%; width: auto;" class="well well-sm">
                            <h4>Комментарии:</h4>
                            <?php if(!empty($model->ticketComments)): ?>
                                <?php $start = count($model->ticketComments)-3; $start = $start >= 0 ? $start : 0; ?>
                                <?php $last3 = array_slice($model->ticketComments,$start,3); ?>
                                <?php foreach($last3 as $last): ?>
                                    <p><strong><?= substr($last->created_at,0,16); ?> <?= $last->author->name.' '.$last->author->surname; ?> - </strong> <?= $last->text; ?></p>
                                <?php endforeach; ?>
                            <?php else: ?>
                                Нет комментариев
                            <?php endif; ?>
                            <a href="<?= Url::to(['/admin/tickets/comments', 'id' => $model->id]); ?>" data-target=".modal" data-toggle="modal" class="btn btn-primary btn-xs">Просмотр</a>
                            <a href="<?= Url::to(['/admin/tickets/leave-comment', 'id' => $model->id]); ?>" data-target=".modal" data-toggle="modal" class="btn btn-primary btn-xs">Добавить</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="timeline-footer">
                <hr>
                <h4>Лог тикета:</h4>
                <p><?= $model->getLogAsString("<br>",3); ?></p>
                <a href="<?= Url::to(['/admin/tickets/full-log', 'id' => $model->id]); ?>" data-target=".modal" data-toggle="modal" class="btn btn-primary btn-xs">Смотреть весь лог</a>
            </div>
        </div>
    </li>
</ul>