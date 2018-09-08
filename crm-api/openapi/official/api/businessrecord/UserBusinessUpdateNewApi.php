<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessNewService;

/**
 * 业务跟进修改
 * @return msg："修改成功",result ：""
 * @author lzk
 */
class UserBusinessUpdateNewApi extends Api
{
    public function run()
    {
        $business_id = \Yii::$app->request->post('business_id');
        $customer_state = \Yii::$app->request->post('customer_state');
        $customer_priority = \Yii::$app->request->post('customer_priority');
        $customer_longitude = \Yii::$app->request->post('customer_longitude');
        $customer_latitude = \Yii::$app->request->post('customer_latitude');
        $customer_photo_str = \Yii::$app->request->post('customer_photo_str');
        $followup_text = \Yii::$app->request->post('followup_text');
        $user_longitude = \Yii::$app->request->post('user_longitude');
        $user_latitude = \Yii::$app->request->post('user_latitude');
        $service = UserBusinessNewService::instance();
        $res = $service->userBusinessUpdate($business_id,$customer_state,$customer_priority,$customer_longitude,$customer_latitude,$customer_photo_str,$followup_text, $user_longitude, $user_latitude);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'提交成功',result=>$res];
    }
}