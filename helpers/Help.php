<?php

namespace app\helpers;

use pimax\FbBotApp;
use pimax\Messages\Message;
use Yii;

class Help
{
    /**
     * Дебаг различных переменных
     * @param $var
     * @param bool|false $return
     * @return string
     */
    public static function debug($var, $return = false)
    {
        $result = "";
        //debug
        ob_start();
        print_r($var);
        $out = ob_get_clean();

        if(!$return){
            echo "<pre>";
            echo htmlentities($out);
            echo "</pre>";
        }else{
            $result = "<pre>".htmlentities($out)."</pre>";
        }

        return $result;
    }

    /**
     * Send message as adminizator bot
     * @param $recipient
     * @param $message
     * @return mixed
     */
    public static function azsend($recipient,$message)
    {
        $bot = new FbBotApp(Yii::$app->params['fb-adminizator-page-key']);
        $result = $bot->send(new Message($recipient,$message));
        return $result;
    }

    /** Pseudo-async cURL requests
     * @param $urls
     * @return string
     */
    public static function multicurl($urls = [])
    {
        $multi = curl_multi_init();
        $channels = array();

        foreach ($urls as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_multi_add_handle($multi, $ch);

            $channels[$url] = $ch;
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi) == -1) {
                continue;
            }

            do {
                $mrc = curl_multi_exec($multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        foreach ($channels as $channel) {
            echo curl_multi_getcontent($channel);
            curl_multi_remove_handle($multi, $channel);
        }

        curl_multi_close($multi);
    }

    /**
     * If site redirects to some url - this will return this url
     * @param $url
     * @return null|string
     */
    public static function redirurl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0");
        curl_exec($ch);

        $response = curl_exec($ch);
        preg_match_all('/^Location:(.*)$/mi', $response, $matches);
        curl_close($ch);

        return !empty($matches[1]) ? trim($matches[1][0]) : null;
    }

    /**
     * Логирование в файл
     * @param $filename
     * @param $text
     * @return bool|int
     */
    public static function log($filename,$text)
    {
        $log = date('Y-m-d H:i:s',time()).' - '.$text."\n";

        try{
            return file_put_contents($filename, $log, FILE_APPEND);
        }catch (\Exception $ex){
            return false;
        }
    }

    /**
     * Генерация случайной строки заданной длины
     * @param int $length
     * @param bool|false $numbersOnly
     * @return string
     */
    public static function rndstr($length = 10,$numbersOnly = false) {

        $charactersNr = '0123456789';
        $charactersChar = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = $numbersOnly ? $charactersNr : $charactersNr.$charactersChar;

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}