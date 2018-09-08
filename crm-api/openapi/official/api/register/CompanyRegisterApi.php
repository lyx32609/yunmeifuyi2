<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\CompanyRegisterService;

class CompanyRegisterApi extends Api
{
	public function run()
	{
		$username = \Yii::$app->request->post('username');
		$password = \Yii::$app->request->post('password');
		$name = \Yii::$app->request->post('name');
		$phone = \Yii::$app->request->post('phone');
		$area_id = \Yii::$app->request->post('area_id');
		$domain_id = \Yii::$app->request->post('domain_id');
		$status = \Yii::$app->request->post('status');
		$failure = \Yii::$app->request->post('failure');
		$license_num = \Yii::$app->request->post('license_num');
		$register_money = \Yii::$app->request->post('register_money');
		$business = \Yii::$app->request->post('business');
		$business_ress = \Yii::$app->request->post('business_ress');
		$staff_num = \Yii::$app->request->post('staff_num');
		$acting = \Yii::$app->request->post('acting');
		$proxy_level = \Yii::$app->request->post('proxy_level');
		$service_area = \Yii::$app->request->post('service_area');
		$distribution_merchant = \Yii::$app->request->post('distribution_merchant');
		$distribution_car = \Yii::$app->request->post('distribution_car');
		$distribution_staff = \Yii::$app->request->post('distribution_staff');
		$goods_num = \Yii::$app->request->post('goods_num');
		$product = \Yii::$app->request->post('product_type');
		$service_type = \Yii::$app->request->post('service_type');
		$goods = \Yii::$app->request->post('goods_type');
		$salas_business = \Yii::$app->request->post('salas_business');
		$license_image = \Yii::$app->request->post('license_image');
		$user_image_negative = \Yii::$app->request->post('user_image_negative');
		$user_image_positive = \Yii::$app->request->post('user_image_positive');
		$service = CompanyRegisterService::instance();
		$result = $service->companyRegister($username, $password, $name, $phone, $area_id, $domain_id, $status, $failure, $license_num, $register_money, $business, $business_ress, $staff_num, $acting, $proxy_level, $service_area, $distribution_merchant, $distribution_car, $distribution_staff, $goods_num, $product, $goods, $service_type,$salas_business, $license_image, $user_image_negative, $user_image_positive);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}