<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
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
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="container">
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; "Единая сеть РА" <?= date('Y') ?></p>
        <p class="pull-right">
            <?php if(!Yii::$app->user->isGuest): ?>
                <a href="<?= \yii\helpers\Url::to(['/site/logout']); ?>" class="btn btn-primary">Выйти</a>
            <?php endif; ?>
        </p>
    </div>
</footer>

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
