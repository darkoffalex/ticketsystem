<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = $model->isNewRecord ? 'Создать тикет' : 'Обновить тикет';
$this->params['breadcrumbs'][] = ['label' => 'Список пользователей', 'url' => Url::to(['/admin/users/index'])];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;

/* @var $model \app\models\Ticket */
/* @var $user \app\models\User */
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Настройки</h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-ticket-form',
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

                <?php $model->author_name = $user->name.' '.$user->surname; ?>
                <?= $form->field($model, 'author_name'); ?>
                <?= $form->field($model, 'phone_or_email'); ?>
                <?= $form->field($model, 'link')->textInput()->label('Ссылка на файл/комментарий с нарушением <a data-target=".modal" data-toggle="modal" href="'.Url::to(['/site/where-to-get']).'">(где взять?)</a>'); ?>
                <?= $form->field($model, 'text')->textarea(); ?>
                <?= $form->field($model, 'performer_id')->widget(Select2::classname(),[
                    'initValueText' => !empty($model->performer) ? $model->performer->name.' '.$model->performer->surname : '',
                    'options' => ['placeholder' => 'Найти пользователя'],
                    'language' => Yii::$app->language,
                    'theme' => Select2::THEME_DEFAULT,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 2,
                        'language' => [
                            'noResults' => new JsExpression("function () { return 'Нет результатов';}"),
                            'searching' => new JsExpression("function () { return 'Поиск...'; }"),
                            'inputTooShort' => new JsExpression("function(args) {return 'Впишите больше символов'}"),
                            'errorLoading' => new JsExpression("function () { return 'Ожидание...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/admin/users/ajax-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                    ],

                ]); ?>

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
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?php echo Url::to(['/admin/tickets/index']); ?>">Назад</a>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
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