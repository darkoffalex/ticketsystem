<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $controller \app\controllers\SiteController */
/* @var $user \yii\web\User */

$this->title = 'Главная страница сайта';
$controller = $this->context;
$user = Yii::$app->user->identity;
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Супер-пупер тикет система</h1>
        <p class="lead">Выберите что вам нужно</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-3">
                <h2>Жалоба</h2>
                <p>Кто-то что то нарушил и вы жаждите справедливости и неименуегого воздаяния для нарушителя ? Тогда вам сюда!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/complaint']); ?>">Жалоба &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Предложение</h2>
                <p>Хотите что-топредлодить, считаете что знаете как лучше ? Вам сюда!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/offer']); ?>">Предложить &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Отзыв</h2>
                <p>Хотите поблагодарить нас за наши труды и добросовестную работу ? Вперед, мы будем только рады!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/comment']); ?>">Оставить отзыв &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Вопрос</h2>
                <p>Вас мучает вопрос ? Почему бы не задать его нам ? Вперед!</p>
                <p><a class="btn btn-default" href="<?= Url::to(['/question']); ?>">Задать вопрос &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
