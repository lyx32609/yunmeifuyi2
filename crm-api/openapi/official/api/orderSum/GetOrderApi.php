<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19
 * Time: 16:28
 */

namespace official\api\orderSum;


use app\foundation\Api;
use app\services\OrderSumService;

/**
 * Class GetOrderApi
 * 员工提成管理
 * @package official\api\orderSum
 */
class GetOrderApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');

        $service = OrderSumService::instance();
        $result = $service->getOrder($username);
        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}