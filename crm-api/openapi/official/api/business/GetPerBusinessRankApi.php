<?php
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/10 0010
 * Time: 下午 3:48
 */
namespace official\api\business;
use app\foundation\Api;
use app\services\GetPerBusinessRankService;
class GetPerBusinessRankApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post("username");
        $type = \Yii::$app->request->post("type");
        $service = GetPerBusinessRankService::instance();
        $result =$service->getPerRank($username,$type);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];

    }
}