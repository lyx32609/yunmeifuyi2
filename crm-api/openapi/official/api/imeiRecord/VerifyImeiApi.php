<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 11:32
 */
namespace official\api\imeiRecord;

use app\foundation\Api;
use app\services\PhoneImeiService;


/*
 * 验证手机串号
 *
 * */
class VerifyImeiApi extends Api
{
    public function run()
    {
        $imei_number = \Yii::$app->request->post('imei_number');
        $user_id = \Yii::$app->request->post('user_id');

        $service = PhoneImeiService::instance();
        $result = $service->verifyImei($user_id, $imei_number);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
       return ['msg'=>1];
        //账号与串号对应失败
        if(isset($result['ret']) && $result['ret'] == 10)
        {
            return $result;
        }
        //账号未绑定串号
        if(isset($result['ret']) && $result['ret'] == 20)
        {
            return $result;
        }
        //账号已有串号是否修改
        if(isset($result['ret']) && $result['ret'] == 30)
        {
            return $result;
        }
        //账号正在审核
        if(isset($result['ret']) && $result['ret'] == 40)
        {
            return $result;
        }
        //账号与串号对应成功
        return ['msg'=>$result];
    }
}