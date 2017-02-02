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
                    <p><strong>Код чат-бота : </strong><?= $user->bot_key; ?></p>
                    <p><strong>Открытых заявок : </strong><?= count($ticketsOpen); ?></p>
                    <p><strong>Закрытых заявок : </strong><?= count($ticketsClosed); ?></p>
                    <p><strong>Аватара : </strong><img width="45" class="img-thumbnail" src="<?= $user->getAvatar(); ?>"></p>
                    <hr>
                    <p>Чтобы воспользоваться чат-ботом бла-бла-ба-ба код - <?= $user->bot_key; ?> бла-бла-бла-бла ссылка <a target="_blank" href="https://www.facebook.com/RA-Support-1339996612739489/">https://www.facebook.com/RA-Support-1339996612739489/</a></p>
                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
