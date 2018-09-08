<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 查询新增业务
 * @return msg："查询成功",result ：""
 * @author lzk
 */
class UserBusinessDetailApi extends Api
{
    public function run()
    {
        $businessId = \Yii::$app->request->post('businessId');
        $service = UserBusinessService::instance();
        $res = $service->getUserBusinessDetail($businessId);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'查询成功',result=>$res];
    }
}