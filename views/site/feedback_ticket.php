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
                    <?= $form->field($model, 'files[0]',['template' => "<span class='file-label'></span><button type='button' class='btn btn-xs btn-primary delete-file hidden' style='padding: 3px 5px;'>удал.</button>{input}"])->fileInput(['data-model' => 'Ticket'])->label(false)->error(false); ?>
                </div>
                <div style="clear: both;"></div>

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
        var currentIndex = 0;
        var count = 1;

        /**
         * When selected file
         */
        $(document).on('change','input[type="file"]', function(){

            //max file count - 5
            if(count > 4) {
                return false;
            }

            //model name
            var model = $(this).data('model');
            var modelLow = model.toLowerCase();

            //disable file input (can't choose again)
            $(this).addClass('hidden');
            $(this).parent().find('.file-label').html($(this).val());

            //increase index
            currentIndex++;

            //add new file input with delete button
            var blockHtml = '<div class="form-group field-'+modelLow+'-files-'+currentIndex+'"><span class="file-label"></span><button type="button" class="btn btn-xs btn-primary delete-file hidden" style="padding: 3px 5px;">удал.</button><input name="'+model+'[files]['+currentIndex+']" value="" type="hidden"><input id="'+modelLow+'-files-'+currentIndex+'" name="'+model+'[files]['+currentIndex+']" data-model="'+model+'" type="file"></div>';
            $('.file-inputs').append(blockHtml);

            //activate current's input remove button
            $(this).parent().find('.delete-file').removeClass('hidden');

            //update count
            count = $('.file-inputs input[type=file]').length;

            //stop event propagation
            return false;
        });

        /**
         * When clicked - delete
         */
        $(document).on('click','.delete-file', function(){
            $(this).parent().remove();
            return false;
        });
    });
</script>
