<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\IndicatorRecordService;

/**
 * 业务指标记录
 * @return msg："保存成功",result ：""
 * @author lzk
 */
class IndicatorRecordApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $type = \Yii::$app->request->post('type');
        $num = \Yii::$app->request->post('num');
        $service = IndicatorRecordService::instance();
        $res = $service->getIndicatorRecord($user_id,$type,$num);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ["msg" => $res];
    }
}