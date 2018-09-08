<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 16:00
 */
namespace official\api\system;

use app\foundation\Api;
use app\services\SystemService;

/**
 * Class BugRecordApi
 * @package official\api\system
 * 系统记录接口
 */
class BugRecordApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $message = \Yii::$app->request->post('message');
        $time = \Yii::$app->request->post('time');
        $phone = \Yii::$app->request->post('phone');
        $type= \Yii::$app->request->post('type');

        $service = SystemService::instance();
        $result =$service->addBugRecord($user_id, $message, $time, $phone, $type);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
    }
}

