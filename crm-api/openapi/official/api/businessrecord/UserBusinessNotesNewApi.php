<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessNewService;

/**
 * 业务记录查询
 * @return msg："查询成功",result ：""
 * @author lzk
 */
class UserBusinessNotesNewApi extends Api
{
    public function run()
    {
        $business_id = \Yii::$app->request->post('business_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $service = UserBusinessNewService::instance();
        $res = $service->userBusinessNotes($business_id, $is_cooperation);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'查询成功',result=>$res];
    }
}