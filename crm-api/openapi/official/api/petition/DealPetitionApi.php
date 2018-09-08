<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:39
 */
namespace official\api\petition;

use app\foundation\Api;
use app\services\DealPetitionService;

class DealPetitionApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $user_id = \Yii::$app->request->post('user_id');
        $advice = \Yii::$app->request->post('advice');
        $status = \Yii::$app->request->post('status');

        $service = DealPetitionService::instance();
        $result = $service->DealPetition($user_id,$petition_id,$advice,$status);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];

    }
}