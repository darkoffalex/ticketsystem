<?php
namespace app\commands;

use pimax\FbBotApp;
use pimax\Messages\Message;
use yii\console\Controller;
use Yii;

/**
 * Use this controller to run cron actions
 */
class CronController extends Controller
{
    public $message;

    public function options($actionID)
    {
        return ['message'];
    }

    public function optionAliases()
    {
        return ['msg' => 'message'];
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionOut($message = 'hello world')
    {
        echo $message . "\n";
    }
}
