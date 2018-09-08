<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 10:26
 */
namespace official\api\petition;
use \app\foundation\Api;
use app\services\PetitionNewService;

class ManageDetailReceiveNewApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $user_id = \Yii::$app->user->identity->id;

        $service = PetitionNewService::instance();
        $result = $service->ManageDetailReceive($user_id,$petition_id);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}