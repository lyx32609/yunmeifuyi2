<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\GetApproverNewService;

/**
 * Class InquireApproverApi
 * 查询审批人
 * @package official\api\petitionNew
 */
class InquireApproverApi extends Api
{
    public function run()
    {
        $name = \Yii::$app->request->post('name');
        $username = \Yii::$app->request->post('username');
        $user_id = \Yii::$app->request->post('user_id');

        $service = GetApproverNewService::instance();
        $result = $service->InquireApprover($name, $username,$user_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}