<?php
namespace official\api\dataStatistics;

use app\foundation\Api;
use app\services\UserDataStatisticsService;
/**
 *   订单数据统计接口
 *   
 */

class StaffOrdersStatisticsApi extends Api
{
    public function run()
    {
        /** 
         * @var Ambiguous $num 
         * 1 订单总数量
         * 2 订单总金额
         * 3 下单用户数
         * 默认1
         *  */
        $num=\Yii::$app->request->post('num');
        $user = \Yii::$app->request->post('user');
        /**
         * 
         * @var Ambiguous $type  
         * 0.查询本人 
         * 1.查询本组 
         * 2.查询部门
         * 默认为0 
         * 
         * */
        $type = \Yii::$app->request->post('type');
        /**
         * 
         * @var Ambiguous $payment  
         * 1总数据
         * 2买买金
         * 3货到付款
         * 4其他支付方式
         * */
        $payment= \Yii::$app->request->post('payment');
        if(!$num) $num=1;
        if(!$type) $type=0;
        if(!$user) $user=\Yii::$app->user->id;
        if(!$payment) $payment=1;
       
        $arr=[1,2,3];
        if(!in_array($num,$arr))
        {
            return [
                'ret'=>100,
                'msg'=>'数据编号错误',
            ];
        }
        $service = UserDataStatisticsService::instance();
        $result = $service->getStaffRecord($user,$type,$num,$payment);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$result];
    }
}