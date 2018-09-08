<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserRecordNewService;

/**
 * 业务记录查询接口
 * @return array registerNum:{day:”22”, week :”55”, month :”102”} orderNum:{day:”22”, week :”55”, month :”102”} orderPrice:{day:”22”, week :”55”, month :”102”} VisitUser:{day:”22”, week :”55”, month :”102”}
 * @author lzk
 */
class UserRecordNewApi extends Api
{
    public function run()
    {
        
        $num=\Yii::$app->request->post('num');
        $user = \Yii::$app->request->post('user');
        $type = \Yii::$app->request->post('type');
        /* 
         * $num 
         *  1 累计拜访客户
         *  2 累计注册量
         *  3 累计自己注册   （预留，目前返回的是2）
         *  4 累计订单量
         *  5 累计订单金额
         *  6 订单用户数量
         *  7 预存款
         *  8 预存款订单统计
         *  9 买买金订单量
         *  10 买买金订单金额
         *  11 买买金订单用户数
         *  */
        if(empty($num))
        {
            $num=1;
        }
        $arr=[1,2,3,4,5,6,7,8,9,10,11];
        if(!in_array($num,$arr))
        {
            return [
                'ret'=>100,
                'msg'=>'数据编号错误',
            ];
        }
 
        $service = UserRecordNewService::instance();
        $data = $service->getUserRecord($user,$type,$num);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $data;
    }
}