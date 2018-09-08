<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 11:17
 */
namespace official\api\orderSum;

use app\foundation\Api;
use app\services\OrderRateService;

/**
 * Class WithdrawRecordApi
 * @package official\api\orderSum
 * 获取提现记录接口
 */
class WithdrawRecordApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');
        $page_size = \Yii::$app->request->post('page_size');
        $page_count = \Yii::$app->request->post('page_count');

        $service = OrderRateService::instance();
        $result = $service->withDrawRecord($username, $start_time, $end_time, $page_count, $page_size);

        if($result === false) {

            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}

