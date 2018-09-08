<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 11:28
 */
namespace official\api\orderSum;

use app\foundation\Api;
use app\services\OrderRateService;

/**
 * Class OrderRateApi
 * @package official\api\orderSum
 * 转账到买买金接口
 */
class OrderRateApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $money = \Yii::$app->request->post('money');

        $service = OrderRateService::instance();
        $result = $service->Withdraw($username,$money);
        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}