<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $this \yii\web\View */
/* @var $controller \app\controllers\SiteController */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title">Где взять</h4>
    </div>

    <div class="modal-body">
        <img src="<?= Url::to('@web/img/wg_d_1.png'); ?>">
        <img src="<?= Url::to('@web/img/wg_d_2.png'); ?>">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
    </div>
