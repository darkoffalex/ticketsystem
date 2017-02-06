<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Ticket */
/* @var $user \app\models\User */
/* @var $tickets \app\models\Ticket[] */

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
                    <h3 class="box-title">Заявки</h3>
                </div><!-- /.box-header -->
                <div class="box-body">

                    <?php if(!empty($tickets)): ?>
                        <ul class="todo-list">
                            <?php foreach($tickets as $ticket): ?>
                                <li class="">


                                    <small class="text">№ - <?= $ticket->id; ?></small>
                                    <small class="text text-muted"><?= $ticket->created_at; ?></small>

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

                                    <div class="tools">
                                        <a href="<?= Url::to(['/profile/show-ticket','id' => $ticket->id]) ?>">Посмотреть запрос</a>
                                        <?php if($ticket->status_id == Constants::STATUS_NEW): ?>
                                            | <a data-confirm="Вы действительно хотите удалить вашу заявку ?" href="<?= Url::to(['/profile/delete-ticket','id' => $ticket->id]) ?>">Удалить запрос</a>
                                        <?php endif; ?>
                                    </div>

                                    <hr style="margin-top: 10px;margin-bottom: 10px;">

                                    <span class="text"><?= $ticket->text; ?></span>

                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>У вас нет заявок</p>
                    <?php endif; ?>

                </div><!-- /.box-body -->
                <div class="box-footer clearfix no-border">
                    <a href="<?= Url::to(['/site/complaint']); ?>" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Новая заявка</a>
                </div>
            </div>
        </div>
    </div>
</div>
