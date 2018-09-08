<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignService;

/**
 * 外勤签到
 * @return msg："保存成功",result ：""
 * @author lzk
 */
class UserSignApi extends Api
{
    public function run()
    {
        $user = \Yii::$app->user->id;
        $type = \Yii::$app->request->post('type');
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $image = \Yii::$app->request->post('image');
        $path = \Yii::$app->request->post('path');
        $company_categroy_id = \Yii::$app->request->post('company_categroy_id');
        $service = UserSignService::instance();
        $res = $service->add($user, $type, $longitude, $latitude, $image, $company_categroy_id, $path);
        if(isset($res['ret']) && $res['ret'] == 28){
            return $res;
        } else if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}