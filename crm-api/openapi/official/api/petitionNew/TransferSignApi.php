<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 10:24
 */
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\TransferSignService;

class TransferSignApi extends Api
{
    public function run()
    {
        //当前登录人id
        $user_id = \Yii::$app->request->post('user_id');
        //签呈ID
        $petition_id = \Yii::$app->request->post('petition_id');

        //审批意见
        $advice = \Yii::$app->request->post('advice');

        //转签的人id  格式1,2,3
        $tranfer_id= \Yii::$app->request->post('tranfer_id');

        $service = TransferSignService::instance();

        $result = $service->TransferSign($user_id,$petition_id,$advice,$tranfer_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }
}