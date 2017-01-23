<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
/* @var $type string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\Constants;
use yii\helpers\Url;

$this->title = 'Пожалуйста войдите';
$this->params['breadcrumbs'][] = $this->title;

/* @var $social kartik\social\Module */
$social = Yii::$app->getModule('social');
$callback = Url::to(['/site/fb-login', 'type' => $type],true);
?>


<div class="site-contact" style="max-width: 500px; margin: 0 auto;">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">

            <div class="alert alert-danger">
                Вы не авторизировались. Пожалуйста авторизируйтесь через Facebook бла-бла-ба.
            </div>

            <?php if(Yii::$app->user->isGuest): ?>
                <?= $social->getFbLoginLink($callback,['class'=>'btn btn-primary'],['email']); ?>
            <?php endif; ?>
        </div>
    </div>

</div>
