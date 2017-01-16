<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Ticket */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\Constants;
use yii\helpers\Url;

$this->title = 'Жалоба';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact" style="max-width: 500px; margin: 0 auto;">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
        <div class="alert alert-success">
            Жалоба отправлена
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-12">

                <?php $form = ActiveForm::begin([
                    'id' => 'contact-form',
                    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                    'enableClientValidation'=>false,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}\n",
                        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>

                <?= $form->field($model, 'author_name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'phone_or_email'); ?>

                <hr>
                <div class="row">
                    <div class="col-lg-3">
                        <a class="btn btn-default active" href="<?= Url::to(['/complaint']); ?>">Жалоба</a>
                    </div>
                    <div class="col-lg-3 text-center">
                        <a class="btn btn-default" href="<?= Url::to(['/offer']); ?>">Предложение</a>
                    </div>
                    <div class="col-lg-3 text-center">
                        <a class="btn btn-default" href="<?= Url::to(['/comment']); ?>">Отзыв</a>
                    </div>
                    <div class="col-lg-3">
                        <a class="btn btn-default pull-right" href="<?= Url::to(['/question']); ?>">Вопрос</a>
                    </div>
                </div>
                <hr>

                <?= $form->field($model, 'link')->textInput()->label('Ссылка на файл/комментарий с нарушением <a data-target=".modal" data-toggle="modal" href="'.Url::to(['/site/where-to-get']).'">(где взять?)</a>'); ?>
                <?= $form->field($model, 'text')->textarea(); ?>

                <hr>
                <label>Скринщоты:</label>

                <div class="file-inputs">
                    <?= $form->field($model, 'files[0]')->fileInput()->label(false)->error(false); ?>
                </div>
                <a class="add-more-files" href="#">Еще файл</a>

                <?php if($model->hasErrors('files')): ?>
                <div class="form-group has-error">
                    <p class="help-block help-block-error"><?= $model->getFirstError('files'); ?></p>
                </div>
                <?php endif; ?>
                <hr>

                <div class="form-group">
                    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.add-more-files').click(function(){
            var count = $('.file-inputs input[type=file]').length;
            var blockHtml = '<div class="form-group field-ticket-files-'+count+'"><input name="Ticket[files]['+count+']" value="" type="hidden"><input id="ticket-files-'+count+'" name="Ticket[files]['+count+']" type="file"></div>';
            $('.file-inputs').append(blockHtml);

            if(count > 3){
                $(this).remove();
            }

            return false;
        });
    });
</script>
