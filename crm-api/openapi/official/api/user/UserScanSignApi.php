<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignService;

/**
 * 扫码签到        
 * @return msg："保存成功",result ：""
 * @author lzk
 */
###############################
//暂时未上线  需要修改表，目前禁止使用
###############################
class UserScanSignApi extends Api
{
    public function run()
    {
        exit;
        $user = \Yii::$app->user->id;
        $type = \Yii::$app->request->post('type');
        $identity_id=\Yii::$app->request->post('identity_id');
        $identity_type=\Yii::$app->request->post('identity_type');
        $service = UserSignService::instance();
        $res = $service->scanAdd($user,$type,$identity_id,$identity_type);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}