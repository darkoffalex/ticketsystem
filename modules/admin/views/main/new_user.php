<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user \app\models\User */

$user = Yii::$app->user->identity;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo Url::to(['/']); ?>"><b><?php echo Yii::$app->name; ?></b></a>
    </div>
    <div class="login-box-body text-center">
        <img class="img-circle" width="50" src="<?= $user->getAvatar(); ?>">
        <p>Вы вошли как: <strong><?= $user->name.' '.$user->surname; ?></strong></p>
        <p>Ваша заявка на рассморении бла-бла-бла</p>
        <br>
        <a class="btn btn-primary" href="<?= Url::to(['/admin/main/logout']); ?>">Выход</a>
    </div>
</div>
