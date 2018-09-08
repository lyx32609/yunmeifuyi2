<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 14:39
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

/**
 * Class EvaluateHelpApi
 * @package official\api\help
 * 评价帮助详情
 */
class EvaluateHelpApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $type = \Yii::$app->request->post('type');
        $content_id = \Yii::$app->request->post('content_id');


        $service = HelpService::instance();
        $result = $service->evaluateHelp($content_id, $user_id,$type);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}