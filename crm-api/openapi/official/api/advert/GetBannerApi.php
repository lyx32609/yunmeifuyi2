<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 11:24
 */
namespace official\api\advert;

use app\foundation\Api;
use app\services\AdvertGetBannerService;

class GetBannerApi extends Api
{
    public function run()
    {
        $service = AdvertGetBannerService::instance();
        $result = $service->getBanner();
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }




}