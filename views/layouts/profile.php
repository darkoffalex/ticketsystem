<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-blue layout-top-nav">
<?php $this->beginBody() ?>

<div class="wrapper">

    <div class="content-wrapper">
        <div class="container">

            <div style="max-width: 500px; margin: 0 auto;">
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        $a = Yii::$app->controller->action->id;
                        $c = Yii::$app->controller->id;
                        NavBar::begin([
                            'brandLabel' => $this->title,
                            'brandUrl' => Url::to(['/profile/index'])
                        ]);
                        echo Nav::widget([
                            'items' => [
                                ['label' => 'Мои заявки', 'url' => ['/profile/my-tickets'], 'active' => in_array($a,['index','my-tickets'])],
                                ['label' => 'Параметры', 'url' => ['/profile/settings'], 'active' => $a == 'settings'],
                                ['label' => 'Выйти', 'items' => [
                                    ['label' => 'Выход', 'url' => ['/site/logout']],
                                    ['label' => 'Главная', 'url' => ['/site/index']]
                                ]],
                            ],
                            'options' => ['class' => 'navbar-nav'],
                        ]);
                        NavBar::end();
                        ?>
                    </div>
                </div>
            </div>

            <?= $content ?>
        </div>
    </div>


    <footer class="main-footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.3.0
            </div>
            <strong>&copy; "Единая сеть РА" <?= date('Y') ?>
        </div><!-- /.container -->
    </footer>
</div>



<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.modal').on('hide.bs.modal', function() {
        $(this).removeData();
    });
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
