<?php
/**
 * Created by 祁志飞.
 * User: Administrator
 * Date: 2017/4/27 
 * Time: 上午 11:30
 */
namespace official\api\business;
use app\foundation\Api;
use app\services\GetPerBusinessRankNewService;
class GetPerBusinessRankNewApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post("username");
        $type = \Yii::$app->request->post("type");
        $service = GetPerBusinessRankNewService::instance();
        $result =$service->getPerInfo($username,$type);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];

    }
}