<?php
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/28 0028
 * Time: 下午 1:56
 */
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 业务跟进修改2.1
 * @return msg："修改成功",result ：""
 * @author lzk
 */
class UserBusinessFollowApi extends Api
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
        $service = UserBusinessService::instance();
        $res = $service->userBusinessUpdateNew($business_id,$customer_state,$customer_priority,
            $customer_longitude,$customer_latitude,$customer_photo_str,$followup_text);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [msg=>'提交成功',result=>$res];
    }
}