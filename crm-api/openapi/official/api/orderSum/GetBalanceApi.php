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
 * Class GetBalanceApi
 * @package official\api\orderSum
 * 获取账号余额 接口
 */
class GetBalanceApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');

        $service = OrderRateService::instance();
        $result = $service->getBalance($username);
        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}