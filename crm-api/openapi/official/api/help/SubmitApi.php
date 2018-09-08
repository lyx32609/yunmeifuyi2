<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2
 * Time: 15:36
 */
namespace official\api\help;

use app\foundation\Api;
use app\services\HelpService;

/**
 * Class SubmitApi
 * @package official\api\help
 * 用户反馈接口
 */
class SubmitApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $advice= \Yii::$app->request->post('advice');
        $type_id = \Yii::$app->request->post('type_id');


        $service = HelpService::instance();
        $result = $service->submitAdvice($type_id, $user_id, $advice);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }
}