<?php


namespace official\api\petitionNew;


use app\foundation\Api;
use app\services\PayService;



class PetitionPayApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');    //签呈id
        $user_id = \Yii::$app->request->post('user_id');
        $service = PayService::instance();
        $result = $service->petitionPay($user_id,$petition_id);

        if ($result === false) {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];

    }

}