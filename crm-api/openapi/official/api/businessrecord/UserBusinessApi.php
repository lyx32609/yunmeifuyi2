<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 保存业务
 * @return msg："保存成功",result ：""
 * @author lzk
 */
class UserBusinessApi extends Api
{
    public function run()
    {
        $customer_name = \Yii::$app->request->post('customer_name');
        $customer_user = \Yii::$app->request->post('customer_user');
        $customer_tel = \Yii::$app->request->post('customer_tel');
        $customer_type = \Yii::$app->request->post('customer_type');
        $customer_source = \Yii::$app->request->post('customer_source');
        $customer_state = \Yii::$app->request->post('customer_state');
        $customer_priority = \Yii::$app->request->post('customer_priority');
        $customer_longitude = \Yii::$app->request->post('customer_longitude');
        $customer_latitude = \Yii::$app->request->post('customer_latitude');
        $customer_photo_str = \Yii::$app->request->post('customer_photo_str');
        $customer_business_title = \Yii::$app->request->post('customer_business_title');
        $customer_business_describe = \Yii::$app->request->post('customer_business_describe');
        $staff_num = \Yii::$app->user->id;
        $service = UserBusinessService::instance();
        $res = $service->add($customer_name,$customer_user,$customer_tel,$customer_type,$customer_source,$customer_state,
                             $customer_priority,$customer_longitude,$customer_latitude,
                             $customer_photo_str,$customer_business_title,$customer_business_describe,$staff_num);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}