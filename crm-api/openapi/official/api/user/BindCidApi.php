<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\AccountService;

class BindCidApi extends Api
{
	public function run()
	{
		$username = \Yii::$app->request->post('username');
		$cid = \Yii::$app->request->post('cid');
        $appid = \Yii::$app->request->post('gtid');
        $appkey = \Yii::$app->request->post('gtkey');
        $masterSecret = \Yii::$app->request->post('gtmaster');
        $service =  AccountService::instance();
        $result = $service->updateCid($username,$cid,$appid, $appkey, $masterSecret);
        return ['msg' => $result];
	}
}
