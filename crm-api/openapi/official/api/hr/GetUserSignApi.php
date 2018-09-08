<?php
namespace official\api\hr;

use app\foundation\Api;
use app\services\GetUserSignService;
use Yii;
class GetUserSignApi extends Api
{
    public function run()
    {
        $type = \Yii::$app->request->post('type');
        $user_id = \Yii::$app->request->post('user_id');
        $user_name = \Yii::$app->request->post('user_name');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $service = GetUserSignService::instance();
        $res = $service->getUserSign($type, $user_id, $start, $end, $page, $pageSize, $user_name);
        if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $res];
    }
}