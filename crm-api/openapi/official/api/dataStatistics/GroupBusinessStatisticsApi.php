<?php
namespace official\api\dataStatistics;

use app\foundation\Api;
use app\services\UserDataStatisticsService;

/**
 * 业务记录查询接口
 * 
 */
class GroupBusinessStatisticsApi extends Api
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
        $groupId=\Yii::$app->request->post('groupId');
        if(!$num) $num=5;
        $arr=[5,6,7,8,9];
        if(!in_array($num,$arr))
        {
            return [
                'ret'=>100,
                'msg'=>'数据编号错误',
            ];
        }

        $service = UserDataStatisticsService::instance();
        $result = $service->getGroupRecord($groupId,$num);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$result];
    }
}