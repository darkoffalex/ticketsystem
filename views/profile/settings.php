<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $ticketsOpen app\models\Ticket[] */
/* @var $ticketsClosed app\models\Ticket[] */
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
                    <h3 class="box-title">Параметры</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <p><strong>Имя : </strong><?= $user->getFullName(); ?></p>
                    <p><strong>Код авторизации в Messenger: </strong><?= $user->bot_key; ?></p>
                    <p><strong>Открытых заявок : </strong><?= count($ticketsOpen); ?></p>
                    <p><strong>Закрытых заявок : </strong><?= count($ticketsClosed); ?></p>
                    <p><strong>Аватар : </strong><img width="45" class="img-thumbnail" src="<?= $user->getAvatar(); ?>"></p>
                    <hr>
                    <p>Чтобы получать уведомления в Messenger Facebook о статусах рассмотрения ваших заявок, иметь возможность дополнить свою заявку или ответить на вопрос админа РА не покидая Facebook, скопируйте этот код - <?= $user->bot_key; ?> , затем пройдите по <a target="_blank" href="https://www.facebook.com/RA-Support-1339996612739489/">этой ссылке</a>, нажмите на кнопку "Отправить сообщение" и отправьте этот код.</p>
                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
