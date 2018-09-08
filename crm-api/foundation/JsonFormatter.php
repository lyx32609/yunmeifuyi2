<?php
/**
 * Created by PhpStorm.
 * User: acer
 * Date: 2016/8/31
 * Time: 9:34
 */
namespace app\foundation;

class JsonFormatter extends \yii\web\JsonResponseFormatter {

    /**
     * Formats response data in JSON format.
     * @param Response $response
     */
    protected function formatJson($response)
    {
        parent::formatJson($response);
        $content = $response->content;
        $response->content = str_replace(
            array("\r\n", "\r", "\n","\t", '\r\n'), 
            array('', '', '', '', '<br />'), 
            $content);
    }
}