<?php
namespace official\api\petition;

use app\foundation\Api;
use app\services\DetailPetitionService;

class DetailPetitionApi extends Api
{
    public function run()
    {
        $petition_id = \Yii::$app->request->post('petition_id');
        $service = DetailPetitionService::instance();
        $result = $service->detailPetition($petition_id);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];
    }
}