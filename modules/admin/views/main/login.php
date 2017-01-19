<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\LoginForm */

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

/* @var $social kartik\social\Module */
/* @var $user \app\models\User */
$social = Yii::$app->getModule('social');
$callback = Url::to(['/site/fb-login'],true);

?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo Url::to(['/']); ?>">
            <b><?php echo Yii::$app->name; ?></b>
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Войдите чтобы начать работу</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-flat', 'name' => 'login-button']) ?>
                <?= $social->getFbLoginLink($callback,['class'=>'btn btn-primary btn-flat'],['email']); ?>
            </div>
            <!-- /.col -->
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
