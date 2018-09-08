<?php
namespace official\api\dataStatistics;

use app\foundation\Api;
use app\services\UserDataStatisticsService;

/**
 * 业务记录查询接口
 * 
 */
class StaffBusinessStatisticsApi extends Api
{
    public function run()
    {
        /**
         * 
         * @var Ambiguous $num  
         *  5 累计拜访客户
         *  6 累计注册量
         *  7 累计自己注册   （预留，目前返回的是2）
         *  8 预存款
         *  9 预存款订单统计
         * */
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
        if(!$num) $num=5;
        if(!$type) $type=0;
        if(!$user) $user=\Yii::$app->user->id;
        $arr=[5,6,7,8,9];
        if(!in_array($num,$arr))
        {
            return [
                'ret'=>100,
                'msg'=>'数据编号错误',
            ];
        }
        $service = UserDataStatisticsService::instance();
        $result = $service->getStaffRecord($user,$type,$num);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$result];
    }
}