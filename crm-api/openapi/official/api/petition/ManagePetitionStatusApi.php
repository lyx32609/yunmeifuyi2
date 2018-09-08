<?php

namespace official\api\petition;


use app\foundation\Api;
use app\services\ManageReadStatusService;

/**
 * Class ManagePetitionStatusApi
 * @package official\api\petition
 * 审批人是否已读签呈状态
 */
class ManagePetitionStatusApi extends Api
{
    public function run()
    {
        //签呈信息
        $user_id = \Yii::$app->request->post('user_id');
        $service = ManageReadStatusService::instance();
        $result = $service->gerReadStutas($user_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }

}