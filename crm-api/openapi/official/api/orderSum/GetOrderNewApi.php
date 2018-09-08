<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19
 * Time: 16:28
 */

namespace official\api\orderSum;


use app\foundation\Api;
use app\services\OrderSumNewService;

/**
 * Class GetOrderApi
 * 员工提成管理
 * @package official\api\orderSum
 */
class GetOrderNewApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');

        $service = OrderSumNewService::instance();
        $result = $service->getOrderNew($username, $start_time, $end_time);
        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}