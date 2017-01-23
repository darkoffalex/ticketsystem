<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $controller \app\controllers\SiteController */
/* @var $user \yii\web\User */

$this->title = 'Главная страница сайта';
$controller = $this->context;
$user = Yii::$app->user->identity;

/* @var $social kartik\social\Module */
/* @var $user \app\models\User */
$social = Yii::$app->getModule('social');
$callback = Url::to(['/site/fb-login'],true);
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Система обращений в администрацию<br>"Единой сети РА"</h1>
        <p class="lead">Выберите что вам нужно</p>
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

    </div>
</div>
