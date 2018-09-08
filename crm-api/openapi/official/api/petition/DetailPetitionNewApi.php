<?php
namespace official\api\petition;

use app\foundation\Api;
use app\services\PetitionNewService;

class DetailPetitionNewApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $service = PetitionNewService::instance();
        $result = $service->detailPetition($petition_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}