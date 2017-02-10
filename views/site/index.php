<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $controller \app\controllers\SiteController */
/* @var $user \yii\web\User */

$this->title = 'Система обратной связи "Единой сети РА"';
$controller = $this->context;
$user = Yii::$app->user->identity;

/* @var $social kartik\social\Module */
/* @var $user \app\models\User */
$social = Yii::$app->getModule('social');
$callback = Url::to(['/site/fb-login'],true);
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Система обратной связи с администрацией<br>"Единой сети РА"</h1>
        <p class="lead">С чем вы хотели бы обратиться к нам?<br>Выберите наиболее подходящий вариант ниже<br>или войдите в личный кабинет для<br>работы с вашими заявками.</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-3">
                <h2>Жалоба</h2>
                <p>Хотите анонимно оставить жалобу на нарушение в одной из групп РА? Вам сюда. </p>
                <p><a class="btn btn-default" href="<?= Url::to(['/complaint']); ?>">Жалоба &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Предложение</h2>
                <p>У вас идея или отличное предложение для "Единой сети РА"? Вам сюда!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/offer']); ?>">Предложить &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Отзыв</h2>
                <p>Оставьте ваш отзыв о работе "Единой сети РА"! Ваши добрые слова дают нам стимул, а замечания - помогают стать лучше!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/comment']); ?>">Оставить отзыв &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Вопрос</h2>
                <p>Задайте любой интересующий вопрос администрации "Единой сети РА". Ваш вопрос будет переадресован наиболее компетентному сотруднику и он обязательно ответит вам!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/question']); ?>">Задать вопрос &raquo;</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <h2>Личный кабинет</h2>
                <p>Хотите видеть весь список оставленных вами жалоб, их статус, дополнить их или оставить сообщение поддержке? Вам сюда</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/profile/index']); ?>">Перейти в кабинет &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
