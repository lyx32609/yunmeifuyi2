<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 11:15
 */
namespace official\api\advert;

use app\foundation\Api;
use app\services\AdService;

class GetAdApi extends Api
{
    public function run()
    {
        $city = \Yii::$app->request->post('city');

        $service = AdService::instance();
        $result = $service->getAd($city);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}