<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 业务记录查询
 * @return msg："查询成功",result ：""
 * @author lzk
 */
class UserBusinessNotesApi extends Api
{
    public function run()
    {
        $business_id = \Yii::$app->request->post('business_id');
        
        $service = UserBusinessService::instance();
        $res = $service->userBusinessNotes($business_id);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'查询成功',result=>$res];
    }
}