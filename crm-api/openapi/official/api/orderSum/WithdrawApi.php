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
 * 提现到银行卡接口
 */
class WithdrawApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $money = \Yii::$app->request->post('money');
        $password = \Yii::$app->request->post('password');

        $service = OrderRateService::instance();
        $result = $service->Tixian($username, $money, $password);
        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}