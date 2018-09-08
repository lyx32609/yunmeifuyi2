<?php
namespace official\api\user;

use app\foundation\Api;
use Yii;
use app\services\UserGroupMessageService;


/**
 * 根据区域  部门 列表 接口
 * @return array
 * @author
 */
class GetUserLocationNumApi extends Api
{
    public function run()
    {
        $user = Yii::$app->request->post('user');
        $start = Yii::$app->request->post('start');
        $end = Yii::$app->request->post('end');
        $service = UserGroupMessageService::instance();
        $result=$service->getUserLocationNum($user, $start, $end);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }

}