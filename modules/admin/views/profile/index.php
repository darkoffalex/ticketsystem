<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;

$this->title = 'Настрйоки профиля';
$this->params['breadcrumbs'][] = $this->title;

/* @var $model User */
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Настройки</h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-profile-form',
                'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                'enableClientValidation'=>false,
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}\n",
                    //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>


            <div class="box-body">
                <?php if(!$model->hasErrors() && Yii::$app->request->isPost): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i>Сохранено</h4>
                        Изменения внесены
                    </div>
                <?php endif; ?>

                <?= $form->field($model, 'name')->textInput(); ?>
                <?= $form->field($model, 'surname')->textInput(); ?>
                <?= $form->field($model, 'email')->textInput(); ?>

                <hr>

                <?= $form->field($model, 'username')->textInput(); ?>
                <?= $form->field($model, 'password')->passwordInput(); ?>

                <hr>

                <label>Уведомления через бота</label>
                <p>Вы можете получать уведомления от нашего Facebook бота. Чтобы подключить бота - отправьте
                    ему данную команду : <code><?= $model->bot_key; ?></code>. Стриницу бота вы
                    сможете найти по ссылке <a href="https://www.facebook.com/RA-Support-1339996612739489/">https://www.facebook.com/RA-Support-1339996612739489/</a>.
                    Вы также можете настроить ваши уведомления. После подключения отправьте боту команду <code>get all</code>, чтобы
                    получать уведомления о всех создаваемых тикетах. Чтобы получать уведомления только о тикетах которые назначены каким-то
                    конкретным пользователями введите команду <code>get ID_ПОЛЬЗОВАТЕЛЯ,ID_ПОЛЬЗОВАТЕЛЯ,...</code>. Чтобы узнать ваши текущие настройки - отправьте <code>info</code>
                </p>
                <sctrong>Текущая конфигурация :</sctrong> <?= $model->getBotConfig(); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?php echo Url::to(['/admin/users/index']); ?>">Назад</a>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
</div>