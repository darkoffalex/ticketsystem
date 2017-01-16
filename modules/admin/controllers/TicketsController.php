<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;


class TicketsController extends Controller
{
    public function actionIndex()
    {
        return $this->renderContent('Здесь будут тикеты');
    }
}