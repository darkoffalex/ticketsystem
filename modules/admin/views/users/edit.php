<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;

$this->title = $model->isNewRecord ? 'Создать пользователя' : 'Обновить пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Список пользователей', 'url' => Url::to(['/admin/users/index'])];
$this->params['breadcrumbs'][] = $this->title;

/* @var $model User */
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Настройки</h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-users-form',
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

                <?= $form->field($model, 'role_id')->dropDownList([
                    Constants::ROLE_ADMIN => 'Администратор',
                    Constants::ROLE_REDACTOR => 'Редактор',
                    Constants::ROLE_NEW => 'Новый',
                ]); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?php echo Url::to(['/admin/users/index']); ?>">Назад</a>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
</div>