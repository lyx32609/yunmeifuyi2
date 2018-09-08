<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\foundation\Service;
use app\services\UserBusinessService;

/**
* 根据预存商家id获取当前信息
* @param: saveId : 预存id
* @return: array 返回当前id信息
* @version:2.1
* @author: qizhifei
* @date:2017年3月24日
*/
class GetBusinessSaveApi extends Api
{
    public function run()
    {
        $saveId = \Yii::$app->request->post('saveId');
        $service = UserBusinessService::instance();
        $data = $service->getBusinessSave($saveId);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>[$data]];
    }
}