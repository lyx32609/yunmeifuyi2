<?php
namespace official\api\push; 
use app\services\PushMessageService;
class PushMessageToSingleApi extends api
{
	    public function run()
    {
        $cid = \Yii::$app->request->post('cid');
        $title = \Yii::$app->request->post('title');
        $connect = \Yii::$app->request->post('connect');
        $logo = \Yii::$app->request->post('logo');
        $logoUrl = \Yii::$app->request->post('logoUrl');
        $service = PushMessageService::instance();
        $result = $service->push($cid, $title, $connect, $logo, $logoUrl);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}
?>